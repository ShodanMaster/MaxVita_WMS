<?php

namespace App\Exports\Report;

use App\Models\Grn;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GrnDetailedExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $grn;
 
    /**
     * Constructor to accept GRN data and optional item_id
     */
    public function __construct($grn)
    {
        $this->grn = $grn;
    }

    /**
     * Define Excel column headings
     */
    public function headings(): array
    {
        return [
            'SI.No',
            'GRN Number',
            'Item Name',
            'Serial Number',
            'Net Weight',
            'Batch Number',
            'Price',
            'Total Price',
            'Manufacture Date',
            'Expiry Date',
            'GRN Date/Time',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:K1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            },
        ];
    }

  
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFaeaaaa'],
            ],
        ]);
    }


    public function collection()
    {
        $i = 1; // Initialize counter outside the map

        return collect($this->grn)->flatMap(function ($grn) use (&$i) {

            $subs = $grn->grnSubs;

            return $subs->flatMap(function ($sub) use ($grn, &$i) {

                $barcodes = $sub->barcodes
                    ->where('transaction_type', 1)
                    ->where('transaction_id', $grn->id) ?? collect([null]);

                return $barcodes->map(function ($barcode) use ($grn, $sub, &$i) {

                    return [
                        'SI.No'            => $i++,
                        'grn_number'       => $grn->grn_number ?? '',
                        'item_name'        => ($sub->item->name ?? '') . '/' . ($sub->item->item_code ?? ''),
                        'serial_number'    => $barcode->serial_number ?? '',
                        'net_weight'       => $barcode->net_weight ?? '',
                        'batch_number'     => $barcode->batch_number ?? '',
                        'price'            => $sub->price ?? '',
                        'total_price'      => $sub->total_price ?? '',
                        'manufacture_date' => !empty($sub->date_of_manufacture)
                            ? date('d-m-Y', strtotime($sub->date_of_manufacture))
                            : '',
                        'expiry_date'      => !empty($sub->best_before_date)
                            ? date('d-m-Y', strtotime($sub->best_before_date))
                            : '',
                        'grn_date'         => !empty($grn->created_at)
                            ? \Carbon\Carbon::parse($grn->created_at)->format('d-m-Y H:i:s')
                            : '',
                    ];
                });
            });
        });
    }
}
