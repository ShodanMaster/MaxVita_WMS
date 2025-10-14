<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class BarcodeGenerator
{
    public static function nextNumber($prefixKey)
    {
        $today = date('Y-m-d');
        $prefix = $prefixKey . date('ymd');

        $nextNumber = DB::transaction(function () use ($prefixKey, $today) {
            $sequence = DB::table('barcode_sequences')
                ->where('prefix_key', $prefixKey)
                ->where('sequence_date', $today)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                DB::table('barcode_sequences')->insert([
                    'prefix_key' => $prefixKey,
                    'sequence_date' => $today,
                    'current_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $current = 1;
            } else {
                $current = $sequence->current_number + 1;
                DB::table('barcode_sequences')
                    ->where('prefix_key', $prefixKey)
                    ->where('sequence_date', $today)
                    ->update([
                        'current_number' => $current,
                        'updated_at' => now(),
                    ]);
            }

            return $current;
        });

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
