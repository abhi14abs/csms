<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'transaction_date', 'type', 'status', 'tx_year', 'tx_month', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
