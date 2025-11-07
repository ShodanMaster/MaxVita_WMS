<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'barcode',
        'customer_id',
        'dispatch_id',
        'item_id',
        'bin_id',
        'user_id',
    ];
}
