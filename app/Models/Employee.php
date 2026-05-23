<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'id';

    protected static function booted()
    {
        static::addGlobalScope('society_member', function ($builder) {
            $builder->where('is_society_member', 'YES');
        });
    }

    // Fillable matching the new `employees` table definition
    protected $fillable = [
        'empCode',
        'name',
        'gender',
        'category',
        'education',
        'mobile',
        'email',
        'dateOfAppointment',
        'designationAtAppointment',
        'designationAtPresent',
        'presentPosting',
        'personalFileNo',
        'officeLandline',
        'dateOfBirth',
        'dateOfRetirement',
        'homeTown',
        'residentialAddress',
        'status',
        'current_designation',
        'current_posting',
        'last_transfer_date',
        'current_transfer_id',
        'last_promotion_date',
        'current_promotion_id',
        'promoted',
        'is_society_member',
        'created_at',
        'updated_at',
        'profile_image',
        'office_in_charge',
        'promotee_transferee',
        'pension_file_no',
        'nps',
        'increment_month',
        'probation_period',
        'status_of_post',
        'department_id',
        'seniority_sequence_no',
        'sddlsection_incharge',
        '2021_2022',
        'benevolent_member',
        '2022_2023',
        'increment_individual_selc',
        'office_landline_number',
        'increment_withheld',
        'FR56J_2nd_batch',
        'apar_hod',
        '2023_2024',
        '2024_2025',
        'karmayogi_certificate_completed',
        'monthly_subscription'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'employee_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_number', 'empCode');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designationAtPresent', 'id');
    }

    public function getRetirementFlagAttribute()
    {
        if (!$this->dateOfRetirement) {
            return null;
        }

        $now = \Carbon\Carbon::now()->startOfDay();
        $retirementDate = \Carbon\Carbon::parse($this->dateOfRetirement)->startOfDay();
        
        $daysLeft = $now->diffInDays($retirementDate, false);

        if ($daysLeft < 0) {
            return ['type' => 'dark', 'text' => 'Retired'];
        }

        $monthsLeft = $now->floatDiffInMonths($retirementDate);

        if ($monthsLeft <= 3.0) {
            $monthsText = ceil($monthsLeft) == 1 ? '1 Month' : ceil($monthsLeft) . ' Months';
            return ['type' => 'danger', 'text' => 'Retiring in ' . $monthsText];
        } elseif ($monthsLeft <= 6.0) {
            return ['type' => 'warning', 'text' => 'Retiring in ' . ceil($monthsLeft) . ' Months'];
        }

        return null;
    }
}
