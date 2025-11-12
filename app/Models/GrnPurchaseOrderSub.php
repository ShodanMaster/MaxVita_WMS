<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnPurchaseOrderSub extends Model
{

    protected $fillable = [
        'grn_purchase_order_id',
        'purchase_order_id',
        'purchase_number',
        'item_id',
        'picked_quantity',
    ];
}
