<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\UploadRecipient;
use App\Models\UploadRecipientDetail;
use DataTables;
use Log;

class ReportRecipientController extends Controller implements HasMiddleware
{
    public static function middleware(): array{
         return [
             new Middleware('can:VIEW-REPORT-RECIPIENT', only: ['index','getData']),
         ];
    }


    public function index(){
        return view('report_recipient.index');
    }

    public function getData(Request $request){
        $upload = UploadRecipientDetail::with('batch')
        ->whereHas('batch', function($q){
            $q->where('status', '>', 2);
        })
        ->select('upload_recipient_detail.*');

        $bankAccount = trim($request->bank_account ?? '');
        $phone       = trim($request->phone ?? '');
        $productName = trim($request->product_name ?? '');
        $polNumber   = trim($request->pol_number?? '');

        
        if (!$bankAccount && !$phone && !$productName && !$polNumber) {
            return Datatables::of($upload->whereRaw('0 = 1'))->make(true);
        }
 
        $upload->when($bankAccount, function($query) use ($bankAccount) {
            $query->where('bank_account', $bankAccount);
        })
        ->when($phone, function($query) use ($phone) {
            $query->where('phone', $phone);
        })
        ->when($productName, function($query) use ($productName) {
            $query->where('product_name', $productName);
        })
        ->when($polNumber, function($query) use ($polNumber) {
            $query->where('pol_num', $polNumber);
        });


        return Datatables::of($upload)
        ->editColumn('amount', function ($row) {    
            return 'Rp ' . number_format($row->amount, 0, ',', '.');
        })
        ->editColumn('batch.name', function ($row){
             $url = route('upload-recipient.show', $row->batch->id);
            return '<a href="'.$url.'" target="_blank" class="text-primary fw-bold">'
                . e($row->batch->name) .
               '</a>';
           
        })
        ->editColumn('batch.scheduled_at', function ($row){
            return $row->batch ? date('Y-m-d H:i:s', strtotime($row->batch->scheduled_at)) : '';
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
                $status = '<span class="badge bg-danger" >Failed</span>';
            }   

            return $status;
        })
        ->rawColumns(['amount','status','created_at','scheduled_at','batch.name'])
        ->make(true);

    }

}
