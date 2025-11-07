<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'customer_code',
        'shipping_code',
        'customer_address',
        'zip_code',
        'gst_number',
    ];

    public function dispatches(){
        return $this->morphMany(Dispatch::class, 'dispatch_to');
    }
}
