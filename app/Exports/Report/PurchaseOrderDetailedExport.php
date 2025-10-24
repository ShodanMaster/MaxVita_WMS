<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class PurchaseOrderDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'SI.No',
            'Purchase Order No',
            'Purchase Order Date',
            'Item Name',
            'Order Quantity',
            'Picked Quantity',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:F1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"]
            ]
        ]);
    }

    public function collection()
    {
        $data = $this->data;

        $excelArray = [];

        $i = 1;
        foreach ($data as $d) {
            $datas = [
                'Slno' => $i,
                'purchase_number' => $d["purchase_number"],
                'purchase_order_date' => date('d-m-Y', strtotime($d["purchase_date"])),
                'item_name' => $d["item_name"],
                'order_quantity' => $d["quantity"],
                'picked_quantity' => $d["picked_quantity"],
            ];
            $excelArray[] = $datas;
            $i++;
        }

        return collect($excelArray);
    }
}
