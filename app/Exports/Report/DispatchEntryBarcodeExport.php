<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DispatchEntryBarcodeExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @var array
     */
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
            'Sl. No',
            'Dispatch Number',
            'Dispatch Date',
            'Item',
            'UOM',
            'Barcode',
            'Dispatched Quantity',
            'Scanned By',
            'Scan Time',
        ];
    }

    /**
     * Apply styles after sheet creation
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:I1'; // matches 9 columns
                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
                $sheet->getStyle($cellRange)->getFill()->applyFromArray([
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFAEAAAA'],
                ]);
            },
        ];
    }

    /**
     * Default style for sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFAEAAAA'],
            ],
        ]);
    }

    /**
     * Main data for export
     */
    public function collection()
    {
        return collect($this->data)->map(function ($d, $index) {
            return [
                'Sl. No'              => $index + 1,
                'Dispatch Number'     => $d['dispatch_number'] ?? '',
                'Dispatch Date'       => $d['dispatch_date'] ?? '',
                'Item'                => $d['item_name'] ?? '',
                'UOM'                 => $d['uom'] ?? '-',
                'Barcode'             => $d['barcode'] ?? '-',
                'Dispatched Quantity' => $d['dispatched_quantity'] ?? 0,
                'Scanned By'          => $d['user_name'] ?? '-',
                'Scan Time'           => $d['scan_time'] ?? '-',
            ];
        });
    }
}
