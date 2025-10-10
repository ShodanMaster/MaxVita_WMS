<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Barcode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'serial_number',
        'grn_id',
        'branch_id',
        'location_id',
        'bin_id',
        'item_id',
        'date_of_manufacture',
        'best_before_date',
        'batch_number',
        'price',
        'total_price',
        'shelf_life',
        'spq_quantity',
        'net_weight',
        'grn_spq_quantity',
        'grn_net_weight',
        'barcode_time',
        'status',
        'user_id',
        'qc_approval_status',
    ];

    public static function nextNumber($location){

        $locationPrefix = Location::find($location)->prefix;

        $prefix = $locationPrefix . date('ymd');

        $start = '0000001';

        $len = strlen($prefix);

        $barcode = DB::select("SELECT

            CONCAT( '$prefix',

            LPAD(max_serial_number+1,GREATEST(6,LENGTH(max_serial_number+1)),'0')

            ) serial_number

            FROM

            (SELECT IFNULL(MAX(SUBSTRING(serial_number,$len+1)),'0') max_serial_number

                FROM barcodes WHERE  serial_number LIKE '$prefix%') P2");

        return $barcode[0]->serial_number;
    }

    public function grn(){
        return $this->belongsTo(Grn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
