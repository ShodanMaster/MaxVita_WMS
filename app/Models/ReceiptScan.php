<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptScan extends Model
{
    protected $fillable = [
        'dispatch_id',
        'barcode',
        'item_id',
        'bin_id',
        'scanned_quantity',
        'uom_id',
        'user_id',
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id');
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
        return $this->belongsTo(User::class, 'user_id');
    }
    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }
}
