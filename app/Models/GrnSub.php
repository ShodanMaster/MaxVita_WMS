<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnSub extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'grn_id',
        'item_id',
        'batch_number',
        'date_of_manufacture',
        'best_before_date',
        'total_quantity',
        'number_of_barcodes',
        'scanned_quantity',
        'grn_status',
        'status',
    ];

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function grn()
    {
        return $this->belongsTo(Grn::class, 'grn_id');
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class, 'item_id', 'item_id');
    }
}
