<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionIssue extends Model
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

    public function productionPlanSub()
    {
        return $this->belongsTo(ProductionPlanSub::class, 'production_plan_sub_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
