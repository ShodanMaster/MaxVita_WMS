<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ExpirewiseStockExport implements FromCollection,WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $stock;
    public function __construct($stock)
    {
        $this->stock = $stock;
    }
    public function headings(): array
    {
        return [
            'Sl/No',
            'Item Name',
            'Item Code',
            'Expired',
            'Less than 1 month',
            'Less than 6 months',
            'Less than 1 year',
            'More than 1 year'
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

        $excelArray = [];
        $i = 1;

        foreach ($this->stock as $row) {
            $excelArray[] = [

                'slno'           => $i++,
                'item_name'      => $row['item_name'] ?? '-',
                'item_code'      => $row['item_code'] ?? '-',
                'expired'   => $row['already_expired'] ?? '-',
                'less_than_one_months'   => $row['less_than_one_months'] ?? '-',
                'less_than_six_months'   => $row['less_than_six_months'] ?? 0,
                'less_than_one_year'       => $row['less_than_one_year'] ?? '-',
                'more_than_one_year'   => $row['more_than_one_year'] ?? 0,
            ];
        }

        return collect($excelArray);
    }
 }

