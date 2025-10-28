<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchSub extends Model
{
    protected $fillable = [
        'dispatch_id',
        'item_id',
        'uom_id',
        'total_quantity',
        'dispatch_quantity',
        'status',
    ];
}
