<?php
namespace App\Services;
use Excel;  
use App\Models\UploadRecipient;
use App\Models\UploadRecipientDetail;
use App\Imports\UploadRecipient as UploadRecipientImport;
use Carbon\Carbon;
use Validator;
use DB;
use Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use App\Traits\SendEmail;
use App\Models\User;


class ProcessRecipientService
{
    use SendEmail;

    public function run() {
       $recipient = UploadRecipient::with('creator')->where('status', 0)
       ->orderBy('created_at', 'ASC')
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
                $reader = IOFactory::createReaderForFile($path);
                $reader->setReadDataOnly(true);
                $reader->setReadFilter(new class implements IReadFilter {
                    public function readCell($column, $row, $worksheetName = '')
                    {
                        return $row === 1;
                    }
                });

                $spreadsheet = $reader->load($path);
                $sheet = $spreadsheet->getActiveSheet();
          
                $sms = trim((string) $sheet->getCell('B1')->getValue());
                // Log::channel('cron')->info('SMS Text B1', ['sms' => $sms]);
                $validator = Validator::make(
                    ['sms_text' => $sms],
                    [
                        'sms_text' => [
                            'required',
                            'string',
                            function ($attribute, $value, $fail) {
                                $requiredParams = [
                                    '~product',
                                    '~amount',
                                    '~account',
                                    '~trxdate'
                                ];
                    
                                foreach ($requiredParams as $param) {
                                    if (!str_contains($value, $param)) {
                                        $fail("SMS Text in B1 must required with {$param} parameter.");
                                    }
                                }
                            }
                        ]
                    ],
                    [
                        'sms_text.required' => 'SMS Text B1 is required.',
                    ]
                );

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }

                Excel::import(new UploadRecipientImport($r->id), $path);
                $totalRecipient = UploadRecipientDetail::where('upload_recipient_id', $r->id)->count();
                $totalAmount = UploadRecipientDetail::where('upload_recipient_id', $r->id)->sum('amount');
                $r->update([
                    'status' => 1, 
                    'total_recipient' => $totalRecipient,
                    'total_amount' => $totalAmount,
                    'template' => $sms,
                ]);

                $data = [
                    'name' => $r->name,
                    'original_filename' => $r->original_filename,
                    'total_recipient' => $totalRecipient,
                    'total_amount' => number_format($totalAmount, 0, ',', '.'),
                    'scheduled_at' => Carbon::parse($r->scheduled_at)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($r->created_at)->format('Y-m-d H:i:s'),
                    'created_by' => $r->creator ? $r->creator->name : 'System',
                    'approval_url' => url(route('upload-recipient.show', $r->id)),
                ];

                $html = view('email.approval', $data)->render();
                $checker = User::whereHas('role.permission', function ($q) {
                    $q->where('slug', 'APPROVE-UPLOAD-RECIPIENT');
                })->get();
                

                foreach($checker as $c){
                    $this->sendMail($c->email,'Upload Recipient Approval', $html,'info@get-intouch.com');
                }

            DB::commit();
        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollback();
            $failures = $e->failures(); 
            $failure  = $failures[0] ?? null; 
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