<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAudit extends Model
{
    protected $fillable = ['employee_id', 'financial_year', 'month', 'audited_by', 'remark'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by', 'id');
    }
}
