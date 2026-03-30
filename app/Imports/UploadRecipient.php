<?php

namespace App\Imports;

use App\Models\UploadRecipientDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;


// use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Log;

class UploadRecipient implements 
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    SkipsEmptyRows,
    WithValidation
    
    // WithMultipleSheets
{
    protected $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }


    public function headingRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // Log::channel('cron')->info('Import row', $row);
        return new UploadRecipientDetail([
            'upload_recipient_id' => $this->batchId,
            'phone'        => $this->normalizeIndoPhone($row['mobile_num'] ?? null),
            'pol_num'      => $row['pol_num'] ?? null,
            'bank_br_code' => $row['bank_br_code'] ?? null,
            'product_name' => $row['product_name'] ?? null,
            'bank_account' => $row['bank_account'] ?? null,
            'name'         => $row['full_name'] ?? null,
            'amount'       => $row['jumlah'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            // '*.mobile_num' => ['required','string',
            // 'regex:/^(?:\+62|62|0)8[1-9][0-9]{6,10}$/'],
            '*.mobile_num' => ['required', 'string', 'max:255', 'regex:/^[0-9+]+$/'],
            '*.pol_num' => ['nullable','max:225'],
            '*.bank_account' => ['required','numeric','digits_between:6,25'],
            '*.full_name' => ['required','max:225'],
            '*.jumlah' => ['required','integer','min:1','gt:1000'],
            '*.bank_br_code' => ['nullable','max:225'],
            '*.product_name' => ['nullable','max:225'],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'mobile_num' => 'MOBILE_NUM',
            'pol_num' => 'POL_NUM',
            'bank_br_code' => 'BANK_BR_CODE',
            'product_name' => 'PRODUCT_NAME',
            'bank_account' => 'BANK_ACCOUNT',
            'full_name' => 'FULL_NAME',
            'jumlah' => 'JUMLAH', 
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
            '*.full_name.max' => 'FULL_NAME may not exceed 225 characters',

            '*.jumlah.required' => 'JUMLAH is required',
            '*.jumlah.integer' => 'JUMLAH must be a whole number',
            '*.jumlah.min' => 'JUMLAH must be at least 1',

            '*.bank_br_code.max' => 'BANK_BR_CODE may not exceed 225 characters',
            '*.product_name.max' => 'PRODUCT_NAME may not exceed 225 characters',
            '*.bank_name.max' => 'BANK_NAME may not exceed 225 characters',
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