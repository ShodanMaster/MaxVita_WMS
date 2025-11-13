<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;


class DispatchEntryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles

{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $prn;
    public function __construct($prn)
    {
        $this->prn = $prn;
    }

    public function headings(): array
    {
        return [

            'SI.No',
            'Dispatch No',
            'Dispatch Date',
            'Branch',
            'Location',
            'Dispatch Type',
            'Status',
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
        $data = $this->prn;
        $excelArray = [];
        $i = 1;

        foreach ($this->prn as $row) {
            $excelArray[] = [
                'Slno'         => $i,
                'dispatch_number'      => $row['dispatch_number'] ?? '-',
                'dispatch_date'    => !empty($row['dispatch_date']) ? date('d-m-Y', strtotime($row['dispatch_date'])) : '-',
                'branch_name'  => $row['branch_name'] ?? '-',
                'location_name'    => $row['location_name'] ?? '-',
                'dispatch_type'  => $row['dispatch_type'] ?? 0,
                'status'       => $row['status'] ?? '-',
            ];
            $i++;
        }

        return collect($excelArray);
    }
}
