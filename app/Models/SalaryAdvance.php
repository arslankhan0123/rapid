<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
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
        'permitted_by' => 'nullable|exists:employees,id',
        'amount' => 'required|numeric|min:0',
        'approved_date' => 'nullable|date',
        'repayment_from' => 'nullable|date|after_or_equal:approved_date',
        'interest_percentage' => 'nullable|numeric|min:0',
        'installment_period' => 'nullable|integer|min:1',
        'repayment_amount' => 'nullable|numeric|min:0',
        'installment' => 'nullable|numeric|min:0',
        'paid_amount' => 'nullable|min:0',
        'paid_installment' => 'nullable|integer|min:1',
        'status' => 'nullable|in:active,inactive,completed',
    ];

    /**
     * @var string
     */
    protected $table = 'salary_advances';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'permitted_by',
        'description',
        'amount',
        'approved_date',
        'repayment_from',
        'interest_percentage',
        'installment_period',
        'repayment_amount',
        'installment',
        'status',
        'paid_amount',
        'paid_installment',
        'date',
        'posted',
        'account_id',
        'branch_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'employee_id' => 'integer',
        'permitted_by' => 'integer',
        'description' => 'string',
        'amount' => 'float',
        'approved_date' => 'date',
        'repayment_from' => 'date',
        'interest_percentage' => 'float',
        'installment_period' => 'integer',
        'repayment_amount' => 'float',
        'installment' => 'float',
        'status' => 'string',
    ];

    /**
     * Relationship with the Employee model for the employee who is receiving the loan.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Relationship with the Employee model for the employee who permitted the loan.
     */
    public function permittedBy()
    {
        return $this->belongsTo(Employee::class, 'permitted_by');
    }
    public function Account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    public function formattedDate(): Attribute
    {
        return Attribute::get(function () {
            return $this->date
                ? \Carbon\Carbon::parse($this->date)->format('d-m-Y')
                : null;
        });
    }
    protected $appends = ['formatted_date'];


}
