<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchScan extends Model
{
    protected $fillable = [
        'dispatch_id',
        'barcode',
        'item_id',
        'bin_id',
        'uom_id',
        'dispatched_quantity',
        'user_id',
    ];
}
