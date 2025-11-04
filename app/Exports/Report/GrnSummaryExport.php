<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GrnSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Define Excel headings.
     */
    public function headings(): array
    {
        return [
            'SI.No',
            'GRN Number',
            'Purchase Number',
            'Vendor Name',
            'Invoice Number',
            'Invoice Date',
            'GRN Location',
            'GRN Time',
        ];
    }

    /**
     * Optional: apply styles to header.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:H1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            },
        ];
    }

    /**
     * Style customization.
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"],
            ],
        ]);
    }

    /**
     * Build collection for export.
     */
    public function collection()
    {
        $data = $this->data;
      
        $excelArray = [];
        $i = 1;

        foreach ($data as $d) {
            $excelArray[] = [
                'Sl No'           => $i,
                'grn_number'      => $d['grn_number'] ?? '',
                'purchase_number' => $d['purchase_number'] ?? '',
                'vendor_name'     => $d['vendor_name'] ?? '',
                'invoice_number'  => $d['invoice_number'] ?? '',
                'invoice_date'    => !empty($d['invoice_date'])
                    ? date('d-m-Y', strtotime($d['invoice_date']))
                    : '',
                'grn_location'    => $d['location_name'] ?? '',
                'grn_time'        => !empty($d['created_at'])
                    ? date('d-m-Y H:i:s', strtotime($d['created_at']))
                    : '',
            ];
            $i++;
        }

        return collect($excelArray);
    }
}
