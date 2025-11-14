<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
class SalesReturnDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'SI.No',
            'Dispatch Number',
            'Barcode',
            'Item',
            'Uom',
            'Return spq quqntity',
            'net_weight',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:G1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public  function  styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('A1:G1')->applyFromArray([
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

        foreach ($data as $salesReturn) {
            $arrayData = [];
            $arrayData['slno'] = $i;
            $arrayData['dispatch_no']     = $salesReturn['dispatch_no'] ?? '-';
            $arrayData['barcode']     = $salesReturn['barcode'] ?? '-';
            $arrayData['item_name']       = $salesReturn['item_name'] ?? '-';
            $arrayData['uom']        = $salesReturn['uom'] ?? '-';
            $arrayData['spq']        = $salesReturn['spq'] ?? '-';
            $arrayData['net_weight']        = $salesReturn['net_weight'] ?? '-';

            $excelArray[$i] = $arrayData;
            $i++;
        }

        return collect($excelArray);
    }
}
