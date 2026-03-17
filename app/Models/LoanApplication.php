<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'applicant_name',
        'aadhaar',
        'mobile',
        'designation',
        'ro_hq',
        'dept',
        'service_remaining',
        'email',
        'reason',
        'loan_amount',
        'tenure_months',
        'emi_desired',
        'gross_salary',
        'current_emi',
        'net_salary',
        'previous_loan',
        'loan_outstanding',
        'app_account_id',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function sureties()
    {
        return $this->hasMany(LoanApplicationSurety::class, 'loan_application_id');
    }
}
