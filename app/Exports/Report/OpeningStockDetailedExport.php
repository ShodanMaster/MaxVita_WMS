<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OpeningStockDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
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
            'Opening Number',
            'Item Name',
            'Manufacture Date',
            'Expiry Date',
            'Bin Name',
            'Total Quantity',
            'Number of Barcodes',
            'Batch',
            'Location',
            'Branch',
            'User',
            'status',
        ];
    }

    /**
     * Optional: apply styles to header.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:M1';
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
        $sheet->getStyle('A1:M1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"],
            ],
        ]);
    }
    public function collection()
    {
        $data = $this->data; 
        $excelArray = [];
        $i = 1;

        foreach ($data as $d) {
            $excelArray[] = [
                'Sl No'            => $i++,
                'opening_number'   => $d['opening_number'] ?? '',
                'item_name'        => $d['item_name'] ?? '',
                'manufacture_date'    => $d['manufacture_date'] ?? '',
                'best_before'       => $d['best_before'] ?? '',
                'bin'              => $d['bin'] ?? '',
                'total_quantity'      => $d['total_quantity'] ?? '',
                'number_of_barcodes' => $d['number_of_barcodes'] ?? '',
                'batch'       => $d['batch'] ?? '',
                'location'       => $d['location'] ?? '',
                'branch'       =>$d['branch'] ?? '',
                'user'       => $d['user'] ?? '',
                'status'       => ucfirst($d['status'] ?? 'Unknown'),
            ];
        }

        return collect($excelArray);
    }
}
