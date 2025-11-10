<?php

namespace App\Exports\Report;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailedStockExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $stock;
    protected $i;

    public function __construct($stock, $i)
    {
        $this->stock = $stock;
        $this->i = $i;
    }

    public function view(): View
    {
        return view('report.stock.stockdetailedexcel_view', [
            'stock' => $this->stock,
            'i' => $this->i, // 
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        
    }
}

