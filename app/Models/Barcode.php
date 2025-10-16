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
        'transaction_id',
        'transaction_type',
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

    public static function nextNumber($locationId){

        $locationPrefix = Location::find($locationId)->prefix;
        $prefix = $locationPrefix . date('ymd');

        $row = DB::transaction(function () use ($locationId) {
            $today = date('Y-m-d');
            $sequence = DB::table('barcode_sequences')
                ->where('location_id', $locationId)
                ->where('sequence_date', $today)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                DB::table('barcode_sequences')->insert([
                    'location_id' => $locationId,
                    'current_number' => 1,
                ]);
                $current = 1;
            } else {
                $current = $sequence->current_number + 1;
                DB::table('barcode_sequences')
                    ->where('location_id', $locationId)
                    ->update(['current_number' => $current]);
            }

            return $current;
        });

        return $prefix . str_pad($row, 6, '0', STR_PAD_LEFT);
    }


    public function grn(){
        return $this->belongsTo(Grn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
