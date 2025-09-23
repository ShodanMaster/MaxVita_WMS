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
        'nav_loc_code',
        'location_type',
        'description',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
