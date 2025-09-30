<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'purchase_number',
        'vendor_id',
        'purchase_date',
        'status',
    ];

    public static function nextNumber()
    {
        $prefix = 'PR' . date('ym');
        $len = strlen($prefix);

        $purchaseNumber = DB::select("
            SELECT CONCAT(
                '$prefix',
                LPAD(CAST(IFNULL(MAX(SUBSTRING(purchase_number, $len + 1)), '0') + 1 AS UNSIGNED), 5, '0')
            ) AS purchase_number
            FROM purchase_orders
            WHERE purchase_number LIKE '$prefix%'
        ");

        return $purchaseNumber[0]->purchase_number;
    }

}
