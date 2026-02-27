<?php

namespace App\Imports;

use App\Models\UploadRecipientDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Log;

class UploadRecipient implements 
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    SkipsEmptyRows,
    WithValidation,
    WithMultipleSheets
{
    protected $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

        public function sheets(): array
    {
        return [
            'Sheet1' => $this,
        ];
    }


    public function model(array $row)
    {
     
//   Log::channel('cron')->info('mobile_num value', [$row['mobile_num'] ?? 'KEY NOT FOUND']);
// Log::channel('cron')->info('mobile_num exists', [array_key_exists('mobile_num', $row)]);

        return new UploadRecipientDetail([
            'upload_recipient_id' => $this->batchId,
            'phone'        => $this->normalizeIndoPhone($row['mobile_num'] ?? null),
            'pol_num'      => $row['pol_num'] ?? null,
            'bank_br_code' => $row['bank_br_code_optional'] ?? null,
            'product_name' => $row['product_name_optional'] ?? null,
            'bank_name'    => $row['bank_name'] ?? null,
            'bank_account' => $row['bank_account'] ?? null,
            'name'         => $row['full_name'] ?? null,
            'amount'       => $row['nominal'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.mobile_num' => ['required','string',
            'regex:/^(?:\+62|62|0)8[1-9][0-9]{6,10}$/'],
            '*.pol_num_optional' => ['required'],
            '*.bank_account' => ['required','digits_between:8,25'],
            '*.full_name' => ['required','max:255'],
            '*.nominal' => ['required','integer','min:1'],
            '*.bank_br_code_optional' => ['nullable','max:200'],
            '*.product_name_optional' => ['nullable','max:200'],
            '*.bank_name' => ['required','max:200'],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'mobile_num' => 'MOBILE_NUM',
            'pol_num_optional' => 'POL_NUM',
            'bank_br_code_optional' => 'BANK_BR_CODE',
            'product_name_optional' => 'PRODUCT_NAME',
            'bank_name' => 'BANK_NAME',
            'bank_account' => 'BANK_ACCOUNT',
            'full_name' => 'FULL_NAME',
            'nominal' => 'NOMINAL',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.mobile_num.required' => 'MOBILE_NUM is required',
            '*.pol_num_optional.required' => 'POL_NUM is required',

            '*.bank_account.required' => 'BANK_ACCOUNT is required',
            '*.bank_account.digits_between' => 'BANK_ACCOUNT must be 8–25 digits',

            '*.full_name.required' => 'FULL_NAME is required',
            '*.full_name.max' => 'FULL_NAME may not exceed 255 characters',

            '*.nominal.required' => 'NOMINAL is required',
            '*.nominal.integer' => 'NOMINAL must be a whole number',
            '*.nominal.min' => 'NOMINAL must be at least 1',

            '*.bank_br_code_optional.max' => 'BANK_BR_CODE may not exceed 200 characters',
            '*.product_name_optional.max' => 'PRODUCT_NAME may not exceed 200 characters',
            '*.bank_name_optional.max' => 'BANK_NAME may not exceed 200 characters',
        ];
    }

    private function normalizeIndoPhone($phone)
    {
        if (!$phone) return null;

        $phone = trim((string)$phone);

        if (str_starts_with($phone, '+62')) {
            return '0' . substr($phone, 3);
        }

        if (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        }

        return $phone;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}