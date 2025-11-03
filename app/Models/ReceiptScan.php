<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptScan extends Model
{
    protected $fillable = [
        'dispatch_id',
        'barcode',
        'item_id',
        'bin_id',
        'scanned_quantity',
        'uom_id',
        'user_id',
    ];
}
