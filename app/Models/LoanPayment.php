<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_no',
        'payment_date',
        'amount_paid',
        'interest_component',
        'principal_component',
        'penalty_component',
        'extra_payment',
        'balance_after'
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Account::class, 'loan_id', 'account_id');
    }
}
