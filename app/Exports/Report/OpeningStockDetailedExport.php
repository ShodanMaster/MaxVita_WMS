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
            'Serial Number',
            'Net Weight',
            'Batch',
            'Bin Name',
            'Manufacture Date',
            'Expiry Date',
        ];
    }

    /**
     * Optional: apply styles to header.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:I1';
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
        $sheet->getStyle('A1:I1')->applyFromArray([
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
                'serial_number'    => $d['serial_number'] ?? '',
                'net_weight'       => $d['net_weight'] ?? '',
                'batch_name'       => $d['batch_name'] ?? '',
                'expiry_date'      => $d['expiry_date'] ?? '',
                'manufacture_date' => $d['manufacture_date'] ?? '',
                'bin'              => $d['bin'] ?? '',
            ];
        }

        return collect($excelArray);
    }
}
