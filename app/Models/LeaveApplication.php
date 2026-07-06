<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Leave;
use App\Models\Employee;

class LeaveApplication extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'leave_applications';

    // Specify the fillable attributes
    protected $fillable = [
        'employee_id',
        'leave_id',
        'from_date',
        'end_date',
        'total_days',
        'hard_copy',
        'description',
        'branch_id',
        'approved_by',
        'status',
        'paid_leave_days',
        'paid_leave_amount',
        'ticket_amount',
        'claim_amount'
    ];

    // Define the relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    // Define validation rules
    public static  $rules = [
        'employee_id' => 'required|exists:employees,id',
        'leave_id' => 'required|exists:leaves,id',
        'from_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:from_date',
        'total_days' => 'nullable|integer|min:0',
        'description' => 'nullable|string',
        'branch_id' => 'required',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
