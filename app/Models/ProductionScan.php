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
        'scanned_quantity',
        'net_weight',
        'user_id',
        'scan_time',
    ];
}
