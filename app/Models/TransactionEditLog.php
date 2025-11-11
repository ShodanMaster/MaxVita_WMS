<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionEditLog extends Model
{
    protected $fillable = [
        'transaction_type',
        'transaction_id',
        'document_number',
        'user_id'
    ];
}
