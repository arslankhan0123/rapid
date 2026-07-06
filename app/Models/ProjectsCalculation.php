<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectsCalculation  extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects_calculation';

    protected $fillable = [
        'code',
        'name',
        'customer_name',
        'address',
    ];

    public function values()
    {
        return $this->hasMany(ProjectValue::class);
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function getTotalValuesAttribute()
    {
        return $this->values->sum('net_amount');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses->sum('total_amount');
    }

    public function getNetProfitAttribute()
    {
        return $this->total_values - $this->total_expenses;
    }

    public function partners()
    {
        return $this->hasMany(ProjectCalculationPartner::class, 'projects_calculation_id');
    }

    public function commissions()
    {
        return $this->hasMany(ProjectCommission::class, 'projects_calculation_id');
    }

    public function insurances()
    {
        return $this->hasMany(ProjectInsurance::class, 'projects_calculation_id');
    }
}
