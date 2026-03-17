<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplicationSurety extends Model
{
    use HasFactory;

    protected $table = 'loan_application_sureties';
    protected $fillable = ['loan_application_id', 'employee_id', 'employee_number', 'service_left', 'signature'];

    public function application()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function guarantor()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
