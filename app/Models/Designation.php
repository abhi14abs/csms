<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'grade',
        'group',
        'cpc_level',
        'seventh_cpc_level',
        'sanctioned_strength',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
