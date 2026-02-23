<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use App\Models\UploadRecipient;
use App\Models\UploadRecipientDetail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Log;

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
        return view('upload_recipient.index');
    }

    public function getData(Request $request){
        $upload = UploadRecipient::query();

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
        $upload = UploadRecipientDetail::where('upload_recipient_id',$id);
        return Datatables::of($upload)
        ->editColumn('amount', function ($row) {
            return 'Rp ' . number_format($row->amount, 0, ',', '.');
        })
        ->rawColumns(['amount'])
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


        $batch = UploadRecipient::create([
            'name' => $request->name,
            'scheduled_at' => Carbon::parse($request->schedule_date),
            'notes' => $request->notes,
        ]);

       
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $filename = "batch_{$batch->id}.{$ext}";
        $destination = public_path('uploads/recipient');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $file->move($destination, $filename);
        $batch->update([
            'path' => "uploads/recipient/{$filename}"
        ]);

        

        return redirect()->route('upload-recipient.index')->with('success', 'Data Saved');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $upload = UploadRecipient::with(['approver','creator'])->findOrFail($id);

        return view('upload_recipient.show', compact('upload'));
    }

    public function approve(Request $request, $id){
        $upload = UploadRecipient::where('status',1)->findOrFail($id);
        $action = $request->status;
       
        if($action == 2){
            $upload->update([
                'status' => 2,
                'approved_at' => Carbon::now(),
                'approved_by' => auth()->user()->id
            ]);
            return response()->json(['message' => 'Batch Approved']);
        }elseif($action == -2){
            $upload->update([
                'status' => -2,
                'approved_at' => Carbon::now(),
                'approved_by' => auth()->user()->id
            ]);
            return response()->json(['message' => 'Batch Rejected']);
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
        $upload = UploadRecipient::where('status',1)->findOrFail($id);
        $upload->update([
            'status' => -3
        ]);

        return response()->json(['message' => 'Batch Cancelled']);
    }
}
