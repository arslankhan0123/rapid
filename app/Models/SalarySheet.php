<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySheet extends Model
{
    use HasFactory;
    protected $table = 'salary_sheets';

    protected $fillable = [
        'employee_id',
        'salary_generate_id',
        'basic_salary',
        'salary_advance',
        'gross_salary',
        'state_income_tax',
        'loan',
        'total_bonus',
        'total_allowances',
        'total_commission',
        'total_insurance',
        'total_deduction',
        'net_salary',
        'hourly_deduction',
        'total_overtimes',
        'overtime_hours',
        'absence_hours',
        'worked_hours',
        'working_hours',
        'branch_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryGenerate()
    {
        return $this->belongsTo(SalaryGenerate::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function salaryPayment(){
        return $this->hasOne(SalaryPayment::class,'salary_sheet_id','id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
