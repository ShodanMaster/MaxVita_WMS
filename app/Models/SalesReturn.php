<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_number',
        'barcode',
        'spq',
        'customer_id',
        'dispatch_id',
        'item_id',
        'bin_id',
        'user_id',
    ];


    public static function withNumber(){
        $pre = 'R' . date("ymd");
        $len = strlen($pre);

        $withNumber = DB::select("
            SELECT CONCAT('$pre', LPAD(max_return_number + 1, GREATEST(3, LENGTH(max_return_number + 1)), '0')) return_number
            FROM (
                SELECT IFNULL(MAX(SUBSTRING(`return_number`, $len + 1)), '0') max_return_number
                FROM sales_returns
                WHERE return_number LIKE '$pre%'
            ) p2
        ");

        return $withNumber[0]->return_number;
    }

    public static function withoutNumber()
    {
        $pre = 'RW' . date("ymd");
        $len = strlen($pre);

        $withNumber = DB::select("
            SELECT CONCAT('$pre', LPAD(IFNULL(MAX(CAST(SUBSTRING(return_number, $len + 1) AS UNSIGNED)) + 1, 1), 3, '0')) AS return_number
            FROM (
                SELECT DISTINCT return_number
                FROM sales_returns
                WHERE return_number LIKE '$pre%'
            ) AS distinct_returns
        ");

        return $withNumber[0]->return_number ?? $pre . str_pad(1, 5, '0', STR_PAD_LEFT);
    }

}
