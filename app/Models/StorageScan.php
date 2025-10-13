<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageScan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'barcode',
        'item_id',
        'grn_id',
        'bin_id',
        'scanned_quantity',
        'batch_number',
        'net_weight',
        'user_id',
    ];
}
