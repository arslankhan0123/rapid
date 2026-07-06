<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'insurances';

    public static $rules =  [
        'employee_id' => 'required|exists:employees,id',
        'insurance' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ];

    // Specify which attributes are mass assignable
    protected $fillable = [
        'employee_id',
        'insurance',
        'description',
        'date', 
        'insurance_type',
        'monthly_insurance',
        'hourly_insurance',
        'worked_hours',
    ];

    // Define the relationship with the Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
