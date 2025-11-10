<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_code',
        'address',
        'gst_no',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class, 'branch_id', 'id');
    }
}
