<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeductionType;

class Deduction extends Model
{
    use HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'employee_id' => 'required|exists:employees,id',
        'amount' => 'required',
        'deduction_type_id' => 'required|exists:deduction_types,id',
        'month' => 'nullable', // Ensure it's a valid date in YYYY-MM format
        'date'=>"nullable",
        


    ];

    /**
     * @var string
     */
    protected $table = 'deductions';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'amount',
        'description',
        'deduction_type_id',
        'month',
        'date',
        'posted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];
    public function employee()
    {
        return $this->BelongsTo(Employee::class, 'employee_id');
    }
    public function deductionTypes()
    {
        return $this->BelongsTo(DeductionType::class, 'deduction_type_id');
    }
    public function getMonthAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m'); // Cast datetime to YYYY-MM format
    }
}
