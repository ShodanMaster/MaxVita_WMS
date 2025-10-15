<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_number',
        'plan_date',
        'total_quantity',
        'picked_quantity',
        'item_id',
        'user_id',
        'branch_id',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($plan) {
            $user = Auth::user();
            if ($user) {
                $plan->user_id = $user->id;
                $plan->branch_id = $user->branch_id;
            }
        });
    }

    public static function nextNumber()
    {
        $prefix = 'PP' . date('ym');
        $len = strlen($prefix);

        $planNumber = DB::select("
            SELECT CONCAT(
                '$prefix',
                LPAD(CAST(IFNULL(MAX(SUBSTRING(plan_number, $len + 1)), '0') + 1 AS UNSIGNED), 5, '0')
            ) AS plan_number
            FROM production_plans
            WHERE plan_number LIKE '$prefix%'
        ");

        return $planNumber[0]->plan_number;
    }

    public function productionPlanSubs(){
        return $this->hasMany(ProductionPlanSub::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function productionScans(){
        return $this->hasMany(ProductionScan::class);
    }
}
