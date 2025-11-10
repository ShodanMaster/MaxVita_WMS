<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'prefix',
        'location_code',
        'location_type',
        'description',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_location');
    }

    public function dispatches(){
        return $this->morphMany(Dispatch::class, 'dispatch_to');
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class, 'location_id');
    }
}
