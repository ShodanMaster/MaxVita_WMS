<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [

    ];

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
}
