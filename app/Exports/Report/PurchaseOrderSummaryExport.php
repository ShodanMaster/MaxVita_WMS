<?php

namespace App\Exports\Report;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class PurchaseOrderSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [

            'SI.No',
            'Vendor Name',
            'Purchase Order No',
            'Purchase Order Date',
            'location',
            'branch',
            'Status',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:E1';
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

        foreach ($data as $d) {
            $arrayData['slno'] = $i;
            $arrayData['vendor_name'] = $d["vendor_name"];
            $arrayData['purchase_order_number'] = $d["purchase_number"];
            $arrayData['purchase_order_date'] = date('d-m-Y', strtotime($d["purchase_date"]));
            $arrayData['location'] = $d["location"];
            $arrayData['branch'] = $d["branch"];
            $arrayData['status'] = $d["status"];
            $excelArray[$i] = $arrayData;
            $i++;
        }

        return collect($excelArray);
    }
}
