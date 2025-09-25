<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'vendor_code',
        'location',
        'address',
        'city',
        'state',
        'zip_code',
    ];
}
