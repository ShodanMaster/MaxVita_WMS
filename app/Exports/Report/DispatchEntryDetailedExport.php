<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;


class DispatchEntryDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        
    }


    public function headings(): array
    {
        return [
            'SI.No',
            'Dispatch Number',
            'Dispatch Date',
            'Item Name',
            'Uom',
            'Total Quantity',
            'Dispatched Quantity',
            'Received Quantity',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:H1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('A1:H1')->applyFromArray([
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

        if (empty($this->data)) {
            return collect([]);
        }

        foreach ($this->data as $d) {
            $excelArray[] = [
                'Slno' => $i++,
                'dispatch_number' => $d["dispatch_number"] ?? '',
                'dispatch_date' => $d["dispatch_date"] ?? '',
                'itme_name' => $d["item_name"] ?? '',
                'uom' => $d['uom'] ?? '',
                'total_quantity' => $d["total_quantity"] ?? 0,
                'dispatched_quantity' => $d["dispatched_quantity"] ?? 0,
                'received_quantity' => $d["received_quantity"] ?? 0,
            ];
        }
        return collect($excelArray);
    }
        //
    
}
