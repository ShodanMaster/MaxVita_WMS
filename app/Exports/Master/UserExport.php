<?php

namespace App\Exports\Master;

use App\Enums\PermissionLevel;
use App\Enums\UserType;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromCollection, WithHeadings, WithEvents, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array{
        return [
            'Name',
            'Username',
            'Email',
            'User Type',
            'Location',
            'Permission Level',
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
        return collect(User::with('location')
            ->get(['name', 'username', 'email', 'user_type', 'location_id', 'permission_level'])
            ->map(function ($user) {
                return [
                    'Name' => $user->name,
                    'Username' => $user->username,
                    'Email' => $user->email,
                    'User Type' => UserType::from($user->user_type)->label(),
                    'Location' => $user->location->name ?? 'N/A',
                    'Permission Level' => PermissionLevel::from($user->permission_level)->label(),
                ];
            }));
    }
}
