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
        'spq_quantity',
        'user_id',
        'scan_time',
    ];

    public function productionPlan()
    {
        return $this->belongsTo(ProductionPlan::class, 'production_plan_id', 'id');
    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
