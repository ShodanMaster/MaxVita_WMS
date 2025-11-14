<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class StockOutDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
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
            'Batch',
            'barcode',
            'Location',
            'Reason',
            'Quantity',
            'Done By',
            'Stock Out Time',


        ];
    }

    /**
     * Optional: apply styles to header.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:J1';
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
        $sheet->getStyle('A1:J1')->applyFromArray([
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
                'Item Name'  => $item['item_name'] ?? 'Unknown',
                'Item Type'  => $item['item_type'] ?? '',
                'batch number'  => $item['batch_number'] ?? '',
                'serial_number'  => $item['serial_number'] ?? '',
                'Location'   => $item['Loc_Name'] ?? '',
                'Reason'     => $item['reason'] ?? '',
                'SPQ Qty'    => $item['spq_qty'] ?? 0,
                'done_by'    => $item['done_by'] ?? 0,
                'stock_out_time'    => $item['created_at'] ?? 0,

            ];
            $i++;
        }

        return collect($excelArray);
    }
}
