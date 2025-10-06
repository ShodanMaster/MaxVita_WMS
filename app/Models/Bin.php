<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Bin extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location_id',
        'name',
        'bin_code',
        'bin_type',
        'description',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public static function binCode($binType){

        $len = strlen($binType);

        $binCode = DB::select("
            SELECT CONCAT(
                '$binType',
                LPAD(CAST(IFNULL(MAX(SUBSTRING(bin_code, $len + 1)), '0') + 1 AS UNSIGNED), 5, '0')
            ) AS bin_code
            FROM bins
            WHERE bin_code LIKE '$binType%'
        ");

        return $binCode[0]->bin_code;
    }
}
