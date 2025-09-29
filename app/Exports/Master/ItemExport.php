<?php

namespace App\Exports\Master;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings():array{
        return [
            'Item Code',
            'Item Description',
            'In Stock',
            'Item Group',
            'UOM',
            'GST Rate',
            'SKU Code',
            'SPQ Quantity',
            'Item Type',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:B1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public  function  styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('A1:I1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"]
            ]
        ]);
    }

    public function collection()
    {
        return Item::with('category', 'uom')
            ->get(['id', 'category_id', 'uom_id', 'item_code', 'name', 'in_stock', 'gst_rate', 'sku_code', 'item_type', 'spq_quantity'])
            ->map(function($item){
                return [
                    'Item Code' => $item->item_code,
                    'Item Description' => $item->name,
                    'In Stock' => $item->in_stock,
                    'Item Group' => $item->category->name,
                    'UOM' => $item->uom->name,
                    'GST Rate' => $item->gst_rate,
                    'SKU Code' => $item->sku_code,
                    'SPQ Quantity' => $item->spq_quantity,
                    'Item Type' => $item->item_type,
                ];
            });
    }
}
