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
}
