<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchSub extends Model
{
    protected $fillable = [
        'dispatch_id',
        'item_id',
        'uom_id',
        'total_quantity',
        'dispatched_quantity',
        'received_quantity',
        'status',
    ];

    public function dispatch(){
        return $this->belongsTo(Dispatch::class);
    }

    public function uom(){
        return $this->belongsTo(Uom::class)->withTrashed();
    }

    public function item(){
        return $this->belongsTo(Item::class)->withTrashed();
    }
}
