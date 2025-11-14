<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
class SalesReturnSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'Sales Return Number',
            'Dispatch Number',
            'Item',
            'Customer',
            'Location',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:F1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public  function  styles(Worksheet $sheet)
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

        foreach ($data as $salesReturn) {
            $arrayData = [];
            $arrayData['slno'] = $i;
            $arrayData['sales_return_no'] = $salesReturn['sales_return_no'] ?? '-';
            $arrayData['dispatch_no']     = $salesReturn['dispatch_no'] ?? '-';
            $arrayData['item_name']       = $salesReturn['item_name'] ?? '-';
            $arrayData['customer']        = $salesReturn['customer'] ?? '-';
            $arrayData['location']        = $salesReturn['location'] ?? '-';

            $excelArray[$i] = $arrayData;
            $i++;
        }

        return collect($excelArray);
    }
}
