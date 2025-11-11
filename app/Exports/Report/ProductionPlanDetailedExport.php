<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ProductionPlanDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles

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
            'Plan Number',
            'Plan Date',
            'Branch',
            'Subitem',
            'Total Quantity',
            'Picked Quantity',
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
                'plan_no' => $d["plan_no"] ?? '',
                'plan_date' => $d["plan_date"] ?? '',
                'branch_name' => $d["branch_name"] ?? '',
                'item_name' => $d['item_name'] ?? '',
                'total_quantity' => $d["total_quantity"] ?? 0,
                'picked_quantity' => $d["picked_quantity"] ?? 0,
            ];
        }
        return collect($excelArray);
    }
}
