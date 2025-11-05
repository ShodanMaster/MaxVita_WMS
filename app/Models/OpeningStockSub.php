<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningStockSub extends Model
{
    protected $fillable = [
        'opening_stock_id',
        'item_id',
        'manufacture_date',
        'best_before',
        'bin_id',
        'total_quantity',
        'number_of_barcodes',
        'batch',
        'status',
    ];
}
