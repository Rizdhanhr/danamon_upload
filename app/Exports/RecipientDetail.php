<?php

namespace App\Exports;

use App\Models\UploadRecipientDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use DB;


class RecipientDetail implements FromQuery,
    WithHeadings,
    WithChunkReading,
    WithStyles,
    ShouldAutoSize
{
   protected $uploadId;

    public function __construct($uploadId)
    {
       $this->uploadId = $uploadId;
    }

    public function query()
{
    return UploadRecipientDetail::query()
        ->where('upload_recipient_id', $this->uploadId)
        ->select([
            'phone      as MOBILE_NUM',
            'amount         as JUMLAH',
            'pol_num         as POL_NUM',
            'bank_br_code    as BANK_BR_CODE',
            'product_name    as PRODUCT_NAME',
            'bank_account    as BANK_ACCOUNT',
            'name       as FULL_NAME',
            DB::raw("
                CASE 
                    WHEN valid_phone > 0 THEN 'Y'
                    ELSE 'N'
                END as VALID_PHONE
            "),
            DB::raw("
                CASE 
                    WHEN status < 0 THEN 'Failed'
                    WHEN status = 0 THEN 'Pending'
                    WHEN status = 1 THEN 'On Process'
                    WHEN status = 2 THEN 'On Process'
                    WHEN status = 3 THEN 'Sent'
                    ELSE 'Unknown'
                END as STATUS
            "),
            DB::raw("
                CASE 
                    WHEN status < 0 THEN serial_number
                    ELSE '-'
                END as REMARK
            "),
            DB::raw("
                CASE 
                    WHEN valid_phone > 0 THEN dr_date
                    ELSE '-'
                END as SMS
            "),
        ]);
    }


    public function headings(): array
    {
        return [
            'MOBILE_NUM', 
            'JUMLAH',
            'POL_NUM',
            'BANK_BR_CODE',
            'PRODUCT_NAME',
            'BANK_ACCOUNT',
            'FULL_NAME',
            'VALID_PHONE',
            'STATUS',
            'REMARK',
            'SMS'
        ];
    }


    public function chunkSize(): int
    {
        return 2000;
    }


    public function styles(Worksheet $sheet)
    {
       $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('F1:G1')->applyFromArray([
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);

        return [];
    }

    
}
