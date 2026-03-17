<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAttribute extends Model
{
    use HasFactory;

    protected $table = 'loan_attributes';
    protected $primaryKey = 'loan_id';
    public $incrementing = false;

    protected $fillable = [
        'loan_id',
        'principal_amount',
        'interest_rate',
        'tenure_months',
        'emi_amount',
        'disbursal_date',
        'start_date',
        'penalty_rate',
        'penalty_enabled',
        'is_topup',
        'parent_loan_id'
    ];

    protected $casts = [
        'disbursal_date' => 'datetime',
        'start_date' => 'date',
        'penalty_enabled' => 'boolean',
        'penalty_rate' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Account::class, 'loan_id', 'account_id');
    }
}
