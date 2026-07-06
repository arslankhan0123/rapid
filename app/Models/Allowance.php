<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AllowanceType;

class Allowance extends Model
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
        'allowance_type_id' => 'required|exists:allowance_types,id',
        'date' => "required"

    ];

    /**
     * @var string
     */
    protected $table = 'allowances';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'allowance_type_id',
        'amount',
        'description',
        'date',
        'posted',
        'payment_type',
        'monthly_allowance',
        'hourly_allowance',
        'worked_hours',
    ];

    public const PAYMENT_TYPES = [
        1 => 'Daily',
        2 => 'Weekly',
        3 => 'Twicely',
        4 => 'Monthly',
        5 => "3 Months",
        6 => "6 Months",
        7 => 'Yearly',
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
    public function allowanceTypes()
    {
        return $this->BelongsTo(AllowanceType::class, 'allowance_type_id');
    }
}
