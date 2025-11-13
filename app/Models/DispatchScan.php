<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchScan extends Model
{
    protected $fillable = [
        'dispatch_id',
        'barcode',
        'item_id',
        'bin_id',
        'uom_id',
        'dispatched_quantity',
        'user_id',
    ];

    public function dispatch(){
        return $this->belongsTo(Dispatch::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
