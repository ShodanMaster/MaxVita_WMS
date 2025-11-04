<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GrnItemwiseExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    /**
     * Constructor to accept GRN data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Define Excel column headings
     */
    public function headings(): array
    {
        return [
            'SI.No',
            'GRN Number',
            'Item Name',
            'Quantity',
            'UOM',
            'Batch Number',
            'Net Weight',
            'Manufacture Date',
            'Expiry Date',
            'Number of Barcodes',
        ];
    }

    /**
     * Optional: Apply bold and size styling to header row
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:J1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            },
        ];
    }

    /**
     * Style customization for header background color
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFaeaaaa'],
            ],
        ]);
    }

    /**
     * Build the data collection for export
     */
    public function collection()
    {
        $data = $this->data;
        $excelArray = [];
        $i = 1;

        foreach ($data as $row) {
            $excelArray[] = [
                'Sl No'             => $i,
                'grn_numberr'        => $row['grn_number'] ?? '',
                'item_name'         => $row['item_name'] ?? '',
                'total_quantity'          => $row['total_quantity'] ?? '',
                'uom_name'               => $row['uom_name'] ?? '',
                'batch_number'      => $row['batch_number'] ?? '',
                'net_weight'        => $row['spq_quantity'] ?? '',
                'manufacture_date'  => !empty($row['date_of_manufacture'])
                    ? date('d-m-Y', strtotime($row['date_of_manufacture']))
                    : '',
                'expiry_date'       => !empty($row['best_before_date'])
                    ? date('d-m-Y', strtotime($row['best_before_date']))
                    : '',
                'number_of_barcodes' => $row['number_of_barcodes'] ?? '',
            ];
            $i++;
        }

        return collect($excelArray);
    }
}
