<?php

namespace App\Exports\Master;

use App\Models\Bin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        return[
            'Bin Name',
            'ERP Code',
            'Bin Type',
			'Storage Location',
			'Description',
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

        $sheet->getStyle('A1:E1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => "FFaeaaaa"]
            ]
        ]);
    }

    public function collection()
    {
        return Bin::with('location')
            ->get(['name', 'bin_code', 'bin_type', 'location_id', 'description'])
            ->map(function ($bin) {
                return [
                    'Bin Name' => $bin->name,
                    'ERP Code' => $bin->bin_code,
                    'Bin Type' => $bin->bin_type,
                    'Storage Location' => $bin->location->name ?? 'N/A',
                    'Description' => $bin->description,
                ];
            });
    }

}
