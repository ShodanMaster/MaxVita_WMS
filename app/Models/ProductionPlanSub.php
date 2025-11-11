<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionPlanSub extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'production_plan_id',
        'item_id',
        'total_quantity',
        'picked_quantity',
        'status',
    ];

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function productionPlan()
    {
        return $this->belongsTo(ProductionPlan::class, 'production_plan_id', 'id');
    }

    public function productionIssues()
    {
        return $this->hasMany(ProductionIssue::class, 'production_plan_id');
    }
}
