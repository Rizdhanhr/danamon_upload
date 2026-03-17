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
            'bank_account    as BANK_ACCOUNT',
            'name       as FULL_NAME',
            'phone      as MOBILE_NUM',
            'amount         as NOMINAL',
            'pol_num         as POL_NUM',
            'bank_br_code    as BANK_BR_CODE',
            'product_name    as PRODUCT_NAME',
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
            'serial_number  as SERIAL_NUMBER',
        ]);
    }


    public function headings(): array
    {
        return [
            'BANK_ACCOUNT',
            'FULL_NAME',
            'MOBILE_NUM', 
            'NOMINAL',
            'POL_NUM',
            'BANK_BR_CODE',
            'PRODUCT_NAME',
            'STATUS',
            'SERIAL_NUMBER'
        ];
    }


    public function chunkSize(): int
    {
        return 2000;
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // row header
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'FFFF00'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    
}
