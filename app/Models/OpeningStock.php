<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OpeningStock extends Model
{
    protected $fillable = [
        'opening_number',
        'file_path',
        'user_id',
        'location_id',
        'branch_id',
        'status',
    ];

    public static function openingNumber(){
        $yr = 'OP' . date("y");
        $len = strlen($yr);

        $openingNumber = DB::select("SELECT CONCAT('$yr',LPAD(max_opening_number+1,GREATEST(5,LENGTH(max_opening_number+1)),'0')) opening_number FROM
        (SELECT IFNULL( MAX(SUBSTRING(`opening_number`, $len+1)),'0') max_opening_number FROM opening_stocks WHERE opening_number LIKE '$yr%') p2");

        return $openingNumber[0]->opening_number;
    }

    public function openingStockSubs(){
        return $this->hasMany(OpeningStockSub::class);
    }
}
