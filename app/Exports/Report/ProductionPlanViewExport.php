<?php

namespace App\Exports\Report;

use App\Models\ProductionPlan;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ProductionPlanViewExport implements FromCollection,WithHeadings,ShouldAutoSize,WithStyles
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
            'Plan No',
            'Plan Date',
            'Branch',
            'Item',
            'Planned Qty',
            'Picked Qty',
            'Entry User',
            'Status',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:I1';
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
        $data = $this->prn;
      
        $excelArray = [];
        $i = 1;

        foreach ($this->prn as $row) {
            $excelArray[] = [
                'Slno'         => $i,
                'plan_no'      => $row['plan_no'] ?? '-',
                'plan_date'    => !empty($row['plan_date']) ? date('d-m-Y', strtotime($row['plan_date'])) : '-',
                'branch_name'  => $row['branch_name'] ?? '-',
                'item_name'    => $row['item_name'] ?? '-',
                'planned_qty'  => $row['planned_qty'] ?? 0,
                'picked_qty'   => $row['picked_qty'] ?? 0,
                'entry_user'   => $row['entry_user'] ?? '-',
                'status'       => $row['status'] ?? '-',
            ];
            $i++;
        }

        return collect($excelArray);
    }
}
