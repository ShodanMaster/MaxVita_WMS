<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reason',
        'description',
    ];

    public function stockOuts(){
        return $this->hasMany(StockOut::class);
    }
}
