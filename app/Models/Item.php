<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'uom_id',
        'name',
        'item_code',
        'in_stock',
        'item_type',
        'sku_code',
        'spq_quantity',
        'gst_rate',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function subCategory(){
        return $this->belongsTo(SubCategory::class);
    }

    public function uom(){
        return $this->belongsTo(Uom::class);
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class);
    }
}
