<?php

namespace App\Exports\Report;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceiptBarcodeExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
     * Excel Headings
     */
    public function headings(): array
    {
        return [
            'Sl. No',
            'Dispatch Number',
            'Dispatch Date',
            'Barcode',
            'Item',
            'Bin',
            'Scanned Quantity',
            'UOM',
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
                $cellRange = 'A1:J1'; // matches 9 columns
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
        $sheet->getStyle('A1:J1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFAEAAAA'],
            ],
        ]);
    }

    public function collection()
    {
        $i = 1;

        return collect($this->data)->map(function ($scan) use (&$i) {
            return [
                'Sl. No'           => $i++,
                'Dispatch Number'  => $scan['dispatch_number'] ?? '',
                'Dispatch Date'    => $scan['dispatch_date'] ?? '',
                'Barcode'          => $scan['barcode'] ?? '',
                'Item'             => $scan['item_name'] ?? '',
                'Bin'              => $scan['bin_name'] ?? '-',
                'Scanned Quantity' => $scan['scanned_qty'] ?? 0,
                'UOM'              => $scan['uom'] ?? '',
                'Scanned By'       => $scan['scanned_by'] ?? '',
                'Scan Time'        => $scan['scan_time'] ?? '',
            ];
        });
    }
}
