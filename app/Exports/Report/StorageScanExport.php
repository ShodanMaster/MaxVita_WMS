<?php

namespace App\Exports\Report;

use App\Models\StorageScan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class StorageScanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected   $storage_scan;

    public function __construct($storage_scan)
    {
        $this->storage_scan = $storage_scan;
    }

    public function headings(): array
    {
        return [

            'SI.No',
            'Serial Number',
            'Item Name',
            'Bin',
            'Scanned Qty',
            'Net Weight',
            'Scanned Time',
            'Scanned By',

        ];

       
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:H1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }


    public  function  styles(Worksheet $sheet)
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
        $data = $this->storage_scan; 
       
        $arr = [];
        $i = 1;

        foreach ($data as $dat) {
            $arr[] = [
                'SI.No' => $i++,
                'serial_number' => $dat['serial_number'] ?? $dat['barcode'] ?? '',
                'item_name' => ($dat['item_code'] ?? '') . ' - ' . ($dat['item_name'] ?? ''),
                'bin' => $dat['bin_name'] ?? '',
                'scanned_qty' => $dat['quantity'] ?? $dat['quantity'] ?? '',
                'net_weight' => $dat['net_weight'] ?? $dat['net_weight'] ?? '',
                'scanned_time' => !empty($dat['scan_time']) ? date('d-m-Y H:i:s', strtotime($dat['scan_time'])) : '',
                'scanned_by' => $dat['scanned_by'] ?? '',
            ];
        }

        return collect($arr);
    }
}