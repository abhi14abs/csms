<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'account_id';
    protected $fillable = [
        'employee_id',
        'account_type',
        'current_balance',
        'opened_date',
        'status'
    ];

    protected $casts = [
        'opened_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function loanAttributes()
    {
        return $this->hasOne(LoanAttribute::class, 'loan_id', 'account_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'account_id');
    }

    public function sureties()
    {
        return $this->hasMany(LoanSurety::class, 'loan_id', 'account_id');
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id', 'account_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class, 'loan_id', 'account_id');
    }
}
