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

    public function openingStock(){
        return $this->belongsTo(OpeningStock::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function bin(){
        return $this->belongsTo(Bin::class);
    }
}
