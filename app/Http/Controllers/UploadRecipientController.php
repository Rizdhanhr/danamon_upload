<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use App\Models\UploadRecipient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class UploadRecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
                            if(Gate::allows('DELETE-UPLOAD-RECIPIENT')){
                                $action .= '<li><button type="button"  class="dropdown-item" >Delete</button></li>';
                            }
                    $action .=
                        '</ul>
                    </div>';
                
            return $action;
        })
        ->editColumn('created_at', function ($row){
            return date('Y-m-d H:i:s',strtotime($row->created_at));
        })
         ->editColumn('scheduled_at', function ($row){
            return date('Y-m-d H:i:s',strtotime($row->scheduled_at));
        })
        ->rawColumns(['action','created_at','scheduled_at'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('upload_recipient.create');
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
        $path = $file->storeAs('uploads/recipient', $filename, 'public');
        $batch->update([
            'path' => $path
        ]);

        

        return redirect()->route('upload-recipient.index')->with('success', 'Data Saved');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
