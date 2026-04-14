<?php
namespace App\Services;
use Excel;  
use App\Models\UploadRecipient;
use App\Models\UploadRecipientDetail;
use App\Models\FinanceNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;



class ProcessNotificationService
{
    // use SendEmail;

    public function run() {
       $recipient = FinanceNotification::where('status', 0)
       ->orderBy('created_at', 'ASC')
       ->limit(5)
       ->get();

       foreach($recipient as $r){
          $this->processNotification($r);
       }
    }

    public function processNotification($r){
        try{


         
        }catch(\Exception $e){
            // DB::rollback();
            // $r->update([
            //     'status' => -1,
            //     'exception' => $e->getMessage(),
            // ]);
            throw $e;
        }
    }
}