<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class StockOutSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles

{
    /**
    * @return \Illuminate\Support\Collection
    */  protected $data;

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
            'Item_type',
            'Item',
            'Location',
            'Reason',
            'Quantity',

        ];
    }

    /**
     * Optional: apply styles to header.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:F1';
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
        $sheet->getStyle('A1:F1')->applyFromArray([
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

        foreach ($data as $item) {
            $excelArray[] = [
                'Sl No'      => $i,
                'item_name'  => $item['item_name'] ?? 'Unknown',
                'item_type'  => $item['item_type'] ?? '',
                'location'   => $item['Loc_Name'] ?? '',
                'reason'     => $item['reason'] ?? '',
                'spq_qty'    => $item['spq_qty'] ?? 0,
            ];
            $i++;
        }

        return collect($excelArray);
    }
}
