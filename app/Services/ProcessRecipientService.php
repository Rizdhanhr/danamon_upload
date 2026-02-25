<?php
namespace App\Services;
use Excel;  
use App\Models\UploadRecipient;
use App\Models\UploadRecipientDetail;
use App\Imports\UploadRecipient as UploadRecipientImport;
use DB;


class ProcessRecipientService
{
    public function run() {
       $recipient = UploadRecipient::where('status', 0)
       ->orderBy('created_at', 'ASC')
       ->select('*')
       ->limit(5)
       ->get();

       foreach($recipient as $r){
          $this->processRecipient($r);
       }
    }

    public function processRecipient($r){
        try{
            DB::beginTransaction();
                $path = public_path($r->path);
                Excel::import(new UploadRecipientImport($r->id), $path);
                 Log::channel('cron')->info('sukses');
                $totalRecipient = UploadRecipientDetail::where('upload_recipient_id', $r->id)->count();
                $totalAmount = UploadRecipientDetail::where('upload_recipient_id', $r->id)->sum('amount');
                $r->update([
                    'status' => 1, 
                    'total_recipient' => $totalRecipient,
                    'total_amount' => $totalAmount,
                ]);
               
            DB::commit();
        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollback();
            $failures = $e->failures();   // ARRAY
            $failure  = $failures[0] ?? null; // ambil 1 error aja
            $message = sprintf(
                 '%s at row %d',
                 $failure->errors()[0],
                 $failure->row()
             );
            $r->update([
                'status' => -1,
                'exception' => $message,
            ]);
            throw $e; 
        }catch(\Exception $e){
            DB::rollback();
            $r->update([
                'status' => -1,
                'exception' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}