<?php

namespace App\Exports\Master;

use App\Models\Bin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        return[
            'Bin Name',
            'ERP Code',
			'Storage Location',
			'Description',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:F1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    public function collection()
    {
        return Bin::with('location')
            ->get(['name', 'bin_code', 'location_id', 'description'])
            ->map(function ($bin) {
                return [
                    'Bin Name' => $bin->name,
                    'ERP Code' => $bin->bin_code,
                    'Storage Location' => $bin->location->name ?? 'N/A',
                    'Description' => $bin->description,
                ];
            });
    }

}
