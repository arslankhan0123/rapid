<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    // Specify the table name if it's not the plural form of the model name
    protected $table = 'salaries';

    // Define the fields that can be mass assigned
    protected $fillable = [
        'employee_id',
        'salary',
        'month',
        'is_active',
    ];

    // Define the validation rules
    public static $rules = [
        'employee_id' => 'required|exists:employees,id', // Ensures the employee_id exists in the employees table
        'salary' => 'required|numeric|min:0',
        'month' => 'required|date',
        'is_active' => 'boolean',
    ];

    // Optionally, you can add custom messages for the validation rules
    public static $messages = [
        'employee_id.required' => 'The employee is required.',
        'employee_id.exists' => 'The selected employee does not exist.',
        'salary.required' => 'The salary field is required.',
        'salary.numeric' => 'The salary must be a valid number.',
        'salary.min' => 'The salary must be at least 0.',
        'month.required' => 'The month field is required.',
        'month.date' => 'The month must be a valid date.',
    ];

    // Define the relationship with the Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
