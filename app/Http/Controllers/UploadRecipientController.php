<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use Excel;
use App\Models\FinanceNotification;
use App\Models\UploadRecipient;
use App\Models\UploadRecipientDetail;
use App\Exports\RecipientDetail as RecipientDetailExport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Log;
use DB;
use Auth;

class UploadRecipientController extends Controller implements HasMiddleware
{
     public static function middleware(): array{
         return [
             new Middleware('can:VIEW-UPLOAD-RECIPIENT', only: ['index','getData','getDetailData','show']),
             new Middleware('can:CREATE-UPLOAD-RECIPIENT', only: ['create','store']),
             new Middleware('can:UPDATE-UPLOAD-RECIPIENT', only: ['cancel']),
             new Middleware('can:APPROVE-UPLOAD-RECIPIENT', only: ['approve']),
         ];
    }

    public function index()
    {
        $start = date('Y-m-d',strtotime(Carbon::now()->subMonth(1)));
        $end = date('Y-m-d');
        return view('upload_recipient.index', compact('start','end'));
    }

    public function getData(Request $request){

        $start = $request->start_date; 
        $end   = $request->end_date; 
        $status = $request->status;
           
        
        $upload = UploadRecipient::whereDate('created_at','>=', $start)
        ->whereDate('created_at','<=', $end)
        ->when($status != 'All', function($query) use ($status){
            return $query->where('status', $status);
        });


        return Datatables::of($upload)
        ->addColumn('action', function ($row){
            $action = '';
                    $action .=
                    '<div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <ul class="dropdown-menu">';
                                $action .= '<li><a href="'.route('upload-recipient.show',$row->id).'" type="button" class="dropdown-item">Detail</a></li>';
                            if(Gate::allows('UPDATE-UPLOAD-RECIPIENT') && $row->status === 1){
                                $action .= '<li><button type="button" onclick="cancelConfirm('.$row->id.')" class="dropdown-item">Cancel</button></li>';
                            }
                    $action .=
                        '</ul>
                    </div>';
                
            return $action;
        })
        ->editColumn('created_at', function ($row){
            return date('Y-m-d H:i:s',strtotime($row->created_at));
        })
        ->editColumn('status', function ($row){
            $status = '';
            if($row->status == 0){
                $status = '<span class="badge bg-warning">Uploading</span>';
            }elseif($row->status == 1){
                $status = '<span class="badge bg-secondary">Waiting For Approval</span>';
            }elseif($row->status == 2){
                $status = '<span class="badge bg-success">Approved</span>';
            }elseif($row->status >= 3){
                $status = '<span class="badge bg-success">Completed</span>';
            }elseif($row->status == -1){
                $status = '<span class="badge bg-danger" onclick="showExceptionModal(`'.e($row->exception).'`)">Failed</span>';
            }elseif($row->status == -2){
                $status = '<span class="badge bg-danger">Rejected</span>';
            }elseif($row->status == -3){
                $status = '<span class="badge bg-danger">Canceled</span>';
            }   

            return $status;
        })->editColumn('total_amount', function ($row) {
            return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
        })->editColumn('total_recipient', function ($row) {
            return number_format($row->total_recipient, 0, ',', '.');
        })->editColumn('scheduled_at', function ($row){
            return date('Y-m-d H:i:s',strtotime($row->scheduled_at));
        })
        ->rawColumns(['action','created_at','scheduled_at','status','total_amount','total_recipient'])
        ->make(true);
    }

    public function getDetailData(Request $request,$id){
        $upload = UploadRecipientDetail::with('batch')
        ->select('upload_recipient_detail.*')
        ->where('upload_recipient_id',$id);


        return Datatables::of($upload)
        ->editColumn('amount', function ($row) {
            return 'Rp ' . number_format($row->amount, 0, ',', '.');
        })
        ->editColumn('status', function ($row){
            $status = '';
            if($row->status == 0){
                $status = '<span class="badge bg-warning">Pending</span>';
            }elseif($row->status == 1){
                $status = '<span class="badge bg-secondary">On Process</span>';
            }elseif($row->status == 2){
                $status = '<span class="badge bg-secondary">On Process</span>';
            }elseif($row->status == 3){
                $status = '<span class="badge bg-success">Sent</span>';
            }elseif($row->status < 0){
                $status = '<span class="badge bg-danger" onclick="showFailedModal(`'.e($row->serial_number).'`)">Failed</span>';
            }   

            return $status;
        })
        ->editColumn('valid_phone', function ($row){
            $status = '';
            if($row->status > 0){
                $status = '<span class="badge bg-success">Valid</span>';
            }elseif($row->status == 0){
                $status = '<span class="badge bg-warning">-</span>';
            }else{
                $status = '<span class="badge bg-danger">Invalid</span>';
            }  

            return $status;
        })
        ->rawColumns(['amount','status','valid_phone'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('upload_recipient.create');
    }

    public function download(){
      
       $filePath = public_path('template/excel/template.xlsx');
   
       return response()->download($filePath, 'template_upload_recipient.xlsx');
    }
    public function downloadOriginal($id){

        $upload = UploadRecipient::findOrFail($id);
        $filePath = public_path($upload->path);

       return response()->download($filePath);
    }

    public function export($id){
        $upload = UploadRecipient::where('status',3)->findOrFail($id);
        $filename = "recipient_detail.xlsx";
        return Excel::download(
            new RecipientDetailExport($upload->id),
            $filename
        );

        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'file' => [
                'required',
                'file',
                'mimes:xlsx',
                'max:10240' 
            ],
            'schedule_date' => [
                'required',
                'date'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500'
            ]
        ]);

        try{
            
          
            DB::beginTransaction();

            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();

            $batch = UploadRecipient::create([
                'name' => $request->name,
                'scheduled_at' => Carbon::parse($request->schedule_date),
                'notes' => $request->notes,
                'original_filename' => $originalName,
            ]);

       
       
            $filename = "batch_{$batch->id}.{$ext}";
            $destination = public_path('uploads/recipient');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $batch->update([
                'path' => "uploads/recipient/{$filename}"
            ]);

            DB::commit();

            return redirect()->route('upload-recipient.index')->with('success', 'Data Saved');
        }catch(\Exception $e){
            DB::rollback();
            Log::info($e->getMessage());
            return redirect()->back()->with('error', 'Failed to upload file. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $upload = UploadRecipient::with(['approver','creator'])->findOrFail($id);
        $summary = UploadRecipientDetail::where('upload_recipient_id', $id)
        ->selectRaw("
            SUM(CASE WHEN status IN (0,1,2) THEN amount ELSE 0 END) as pending_amount,
            SUM(CASE WHEN status = 3 THEN amount ELSE 0 END) as sent_amount,
            SUM(CASE WHEN status < 0 THEN amount ELSE 0 END) as failed_amount,
            COUNT(CASE WHEN status IN (0,1,2) THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 3 THEN 1 END) as sent_count,
            COUNT(CASE WHEN status < 0 THEN 1 END) as failed_count
        ")
        ->first();
        return view('upload_recipient.show', compact('upload','summary'));
    }

    public function approve(Request $request, $id){
        try{

            DB::beginTransaction();
            $upload = UploadRecipient::where('status',1)->findOrFail($id);
            $action = $request->status;
            $message = '';
        
            if($action == 2){
                $upload->update([
                    'status' => 2,
                    'approved_at' => Carbon::now(),
                    'approved_by' => auth()->user()->id
                ]);
                $message = 'Batch Approved';
               
            }elseif($action == -2){
                $upload->update([
                    'status' => -2,
                    'approved_at' => Carbon::now(),
                    'approved_by' => auth()->user()->id
                ]);

                $upload->details()
                ->update([
                    'status' => -1,
                    'serial_number' => 'Rejected'
                ]);

                $message = 'Batch Rejected';
            }

            DB::commit();

            return response()->json(['message' => $message]);
        }catch(\Exception $e){
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error_message' => 'Failed to process approval. Please try again.'],500);
        }


        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cancel(string $id)
    {

        try{

            DB::beginTransaction();
                $upload = UploadRecipient::where('status',1)->findOrFail($id);
                $upload->update([
                    'status' => -3
                ]);

                $upload->details()
                ->update([
                    'status' => -1,
                    'serial_number' => 'Canceled'
                ]);
            DB::commit();

            return response()->json(['message' => 'Batch Cancelled']);
        }catch(\Exception $e){
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error_message' => 'Failed to cancel batch. Please try again.'],500);
        }
       
    }

    public function notifyFinance($id){
        try{
            DB::beginTransaction();
              $upload = UploadRecipient::where('status','>',2)->findOrFail($id);
              FinanceNotification::create([
                'upload_recipient_id' => $id,
                'created_by' => Auth::id()
              ]);
              $upload->flag_info_marketing = 1;
              $upload->save();
            DB::commit();
           return redirect()->back()->with('success', 'Email request is being processed');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to notif finance. Please try again.');
        }
    }
}
