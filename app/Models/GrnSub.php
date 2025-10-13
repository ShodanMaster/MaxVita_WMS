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
        'rejected_quantity',
        'grn_status',
        'status',
    ];

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
