<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'awards';

    public static $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'gift' => 'required|string|max:255',
        'date' => 'required|date',
        'employee_id' => 'required|exists:employees,id',
        'award_by' => 'nullable|exists:employees,id',
    ];

    protected $fillable = [
        'name',
        'description',
        'gift',
        'date',
        'employee_id',
        'award_by',
    ];

    // Define relationships

    // Award belongs to Employee (the employee who received the award)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Award belongs to Employee (the employee who gave the award, optional)
    public function awardedBy()
    {
        return $this->belongsTo(Employee::class, 'award_by');
    }
}
