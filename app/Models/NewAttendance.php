<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NewAttendance extends Model
{
    use HasFactory;
    /**
     * Validation rules
     *
     * @var array
     */
    // Validation rules
    public static $rules = [
        'employee_id' => 'required|exists:employees,id',
        'customer_id' => 'nullable|exists:customers,id',
        'project_id' => 'nullable|exists:projects,id',
        'date' => 'required'

    ];
    /**
     * @var string
     */
    protected $table = 'new_attendances';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'customer_id',
        'project_id',
        'date',
        'hours',
        'branch_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
