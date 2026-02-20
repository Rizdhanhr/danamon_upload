<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


trait SendEmail
{
    public function sendMail($email_to, $email_subject, $email_body, $sender_email){
        
        $send = [
            'emailto' => $email_to,
            'emailbcc' => NULL,
            'emailsubject' => $email_subject,
            'emailbody' => $email_body,
            'sender_email' => $sender_email
        ];

        $process = DB::connection('email')->table('emailqueue')->insert($send);

        return $process;
    }

}
