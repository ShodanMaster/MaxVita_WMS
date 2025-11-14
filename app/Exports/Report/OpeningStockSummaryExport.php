<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OpeningStockSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
     * Define Excel headings.
     */
    public function headings(): array
    {
        return [
            'SI.No',
            'Opening Number',
            'Opening Date',
            'User',
            'Location',
            'Branch',
            'Status',
        ];
    }

    /**
     * Optional: apply styles to header.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:H1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            },
        ];
    }

    /**
     * Style customization.
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"],
            ],
        ]);
    }
    public function collection()
    {
        $data = $this->data; 
        $excelArray = [];
        $i = 1;

        foreach ($data as $d) {
            $excelArray[] = [
                'SlNo'           => $i,
                'opening_number'  => $d['opening_number'] ?? '',
                'opening_date'    => $d['opening_date'] ?? '',
                'user'         => $d['user'] ?? '',
                'location'       => $d['location'] ?? '',
                'branch'           => $d['branch'] ?? '',
                'status'          => ucfirst($d['status'] ?? 'Unknown'),
            ];
            $i++;
        }

        return collect($excelArray);
    }
}
