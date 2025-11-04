<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnPurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'grn_id',
        'user_id',
    ];

    public function grn()
    {
        return $this->belongsTo(Grn::class, 'grn_id');
    }


    public function grnWithPosubs()
    {
        return $this->hasMany(GrnPurchaseOrderSub::class, 'grn_with_po_id');
    }
}
