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
            'pol_num         as POL_NUM',
            'bank_br_code    as BANK_BR_CODE',
            'product_name    as PRODUCT_NAME',
            'bank_name       as BANK_NAME',
            'bank_account    as BANK_ACCOUNT',
            'name       as FULL_NAME',
            'amount         as NOMINAL',
            DB::raw("
                CASE 
                    WHEN status < 0 THEN 'Failed'
                    WHEN status = 0 THEN 'Pending'
                    WHEN status = 1 THEN 'On Process'
                    WHEN status = 2 THEN 'Success'
                    ELSE 'Unknown'
                END as STATUS
            "),
            'serial_number   as SERIAL_NUMBER',
        ]);
    }


    public function headings(): array
    {
        return [
            'MOBILE_NUM',
            'POL_NUM',
            'BANK_BR_CODE',
            'PRODUCT_NAME',
            'BANK_NAME',
            'BANK_ACCOUNT',
            'FULL_NAME',
            'NOMINAL',
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
