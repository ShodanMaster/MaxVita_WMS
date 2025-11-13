<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'uom_code',
    ];

    public function items(){
        return $this->hasMany(Item::class);
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class, 'uom_id', 'id');
    }
}
