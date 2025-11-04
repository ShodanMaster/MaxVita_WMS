<?php

namespace App\Exports\Report;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GrnPoWiseExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Excel Headings
     */
    public function headings(): array
    {
        return [
            'SI.No',
            'GRN No',
            'PO Number',
            'Item Name',
            'Quantity',
            'UOM',
            'Batch No',
            'SPQ',
            'Manf. Date',
            'Expiry Date',
            'No. of Barcodes',
        ];
    }

    /**
     * Header Row Styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:K1';
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle($cellRange)->getFont()->setBold(true);
                $sheet->getStyle($cellRange)->getFont()->setSize(12);
            },
        ];
    }

    /**
     * Header Background
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFAEAAAA'],
            ],
        ]);
    }

    /**
     * Build Excel Collection
     */
    public function collection()
    {
        $i = 1;

        $excelData = collect($this->data)->map(function ($row) use (&$i) {
            return [
                'SI.No'           => $i++,
                'GRN No'          => $row['grn_number'] ?? '',
                'PO Number'       => $row['purchase_number'] ?? '',
                'Item Name'       => $row['item_name'] ?? '',
                'Quantity'        => $row['quantity'] ?? '',
                'UOM'             => $row['uom_name'] ?? '',
                'Batch No'        => $row['batch_number'] ?? '',
                'SPQ'             => $row['spq_quantity'] ?? '',
                'Manf. Date'      => $row['date_of_manufacture'] ?? '',
                'Expiry Date'     => $row['best_before_date'] ?? '',
                'No. of Barcodes' => $row['number_of_barcodes'] ?? '',
            ];
        });

        return new Collection($excelData);
    }
}
