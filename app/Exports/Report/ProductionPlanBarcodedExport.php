<?php

namespace App\Exports\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionPlanBarcodedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    
    
    }

    public function headings(): array
    {
        return [
            'Sl. No',
            'Plan Number',
            'Plan Date',
            'Branch',
            'Subitem',
            'Barcode',
            'Scanned Quantity',
            'Net Weight',
            'Scanned By',
            'Scan Time',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:J1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFAEAAAA'],
            ],
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
                'Slno'             => $i++,
                'plan_no'          => $d['plan_no'] ?? '',
                'plan_date'        => $d['plan_date'] ?? '',
                'branch_name'      => $d['branch_name'] ?? '',
                'item_name'        => $d['item_name'] ?? '',
                'barcode'          => $d['barcode'] ?? '',
                'scanned_quantity' => $d['scanned_quantity'] ?? 0,
                'net_weight'       => $d['net_weight'] ?? 0,
                'user_name'        => $d['user_name'] ?? '',
                'scan_time'        => $d['scan_time'] ?? '',
            ];
        }

        return collect($excelArray);
    }
}


