<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Vendor extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'vendor_code',
        'location',
        'address',
        'city',
        'state',
        'zip_code',
    ];

    public static function vendorCode(){
        $prefix = 'VN' . date('ym');
        $len = strlen($prefix);

        $vendorCode = DB::select("
            SELECT CONCAT(
                '$prefix',
                LPAD(CAST(IFNULL(MAX(SUBSTRING(vendor_code, $len + 1)), '0') + 1 AS UNSIGNED), 5, '0')
            ) AS vendor_code
            FROM vendors
            WHERE vendor_code LIKE '$prefix%'
        ");

        return $vendorCode[0]->vendor_code;
    }
}
