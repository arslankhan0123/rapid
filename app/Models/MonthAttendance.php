<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthAttendance extends Model
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
        'customer_id' => 'required|exists:customers,id',
        'project_id' => 'required|exists:projects,id',
        'month'=>'required'
    ];
    /**
     * @var string
     */
    protected $table = 'month_attendances';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'customer_id',
        'project_id',
        'overtime',
        'net',
        'month',

    ];
}
