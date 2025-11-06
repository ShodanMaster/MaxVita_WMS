<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $fillable = [
        'barcode',
        'reason_id;',
        'user_id',
    ];
}
