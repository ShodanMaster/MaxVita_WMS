<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionScan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'barcode',
        'item_id',
        'production_plan_id',
        'bin_id',
        'picked_quantity',
        'net_weight',
        'spq_quantity',
        'user_id',
        'scan_time',
    ];
}
