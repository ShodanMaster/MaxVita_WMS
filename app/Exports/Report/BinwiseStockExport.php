<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class BinwiseStockExport implements FromCollection,WithHeadings,ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $stock;
    protected $data;

    public function __construct($stock)
    {
        $this->stock = $stock;
    }

    public function headings(): array
    {
        return [
            'Sl/No',
            'Branch',
            'Location',
            'Item Name',
            'Batch',
            'Bin Name',
            'Quantity',
            'UOM',
            'SPQ Quantity',
            'Barcode Count',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:J1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
    public  function  styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('A1:J1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"]
            ]
        ]);
    }

    public function collection()
    {
        $excelArray = [];
        $i = 1;

        foreach ($this->stock as $row) {
            $excelArray[] = [
                'slno'           => $i++,
                'branch_name'    => $row['branch_name'] ?? '-',
                'location_name'  => $row['location_name'] ?? '-',
                'item_name'      => $row['item_name'] ?? '-',
                'batch_number'   => $row['batch_number'] ?? '-',
                'bin_name'       => $row['bin_name'] ?? '-', 
                'net_weight'     => $row['net_weight'] ?? 0,
                'uom_name'       => $row['uom_name'] ?? '-',
                'spq_quantity'   => $row['spq_quantity'] ?? 0,
                'count'          => $row['count'] ?? 0,
            ];
        }

        return collect($excelArray);
    }

}
