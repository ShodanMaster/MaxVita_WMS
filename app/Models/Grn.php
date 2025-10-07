<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Grn extends Model
{
    use SoftDeletes;

    public static function grnNumber(){
        $yr = 'GRN' . date("y");
        $len = strlen($yr);

        $grnNumber = DB::select("SELECT CONCAT('$yr',LPAD(max_grn_number+1,GREATEST(5,LENGTH(max_grn_number+1)),'0')) grn_number FROM
        (SELECT IFNULL( MAX(SUBSTRING(`grn_number`, $len+1)),'0') max_grn_number FROM grns WHERE grn_number LIKE '$yr%') p2");

        return $grnNumber[0]->grn_number;
    }
}
