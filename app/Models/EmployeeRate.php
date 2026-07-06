<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRate extends Model
{
    use HasFactory;

    protected $table = 'employee_rates';

    protected $fillable = [
        'employee_id',
        'customer_id',
        'project_id',
        'month',
        'rate',
        'actual_hours',
        'overtime_hours',
        'total_hours',
        'total_absent',
        'net_hours'
    ];
    public static $rules =  [
        'employee_id' => 'required|exists:employees,id',
        'customer_id' => 'required|exists:customers,id',
        'project_id' => 'required|exists:projects,id',
        'month' => 'required',
        'rate'=>"required"
    ];


    public function employeeRate()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


}
