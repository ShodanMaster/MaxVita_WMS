<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionBarcode extends Model
{
    protected $fillable = [
        'production_plan_id',
        'barcode',
        'item_id',
        'total_quantity',
        'balance_quantity',
        'generated_quantity',
        'date_of_manufacture',
        'best_before_date',
        'uom_id',
        'user_id',
    ];
}
