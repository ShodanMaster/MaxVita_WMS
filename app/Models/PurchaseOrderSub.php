<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderSub extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'quantity',
        'picked_quantity',
        'status',
    ];
}
