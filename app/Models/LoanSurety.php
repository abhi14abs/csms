<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSurety extends Model
{
    use HasFactory;

    protected $table = 'loan_sureties';
    protected $fillable = ['loan_id', 'employee_id', 'guarantee_amount'];

    public function loan()
    {
        return $this->belongsTo(Account::class, 'loan_id', 'account_id');
    }

    public function guarantor()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
