<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'dispatch_number',
        'dispatch_date',
        'from_branch_id',
        'from_location_id',
        'dispatch_to',
        'dispatch_time',
        'dispatch_type',
        'driver_name',
        'driver_phone',
        'vehicle_number',
        'user_id',
        'status',
    ];

    public function dispatchTo()
    {
        return $this->morphTo();
    }

    public function dispatchSubs(){
        return $this->hasMany(DispatchSub::class);
    }

    public function dispatchScans(){
        return $this->hasMany(DispatchScan::class);
    }
}
