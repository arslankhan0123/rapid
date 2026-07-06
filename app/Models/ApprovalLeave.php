<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_application_id',
        'approved_by',
        'approved_date',
    ];

    public static $rules = [
        'leave_application_id' => 'required|exists:leave_applications,id',  // Ensures the leave_application_id exists in the leave_applications table
        'approved_by' => 'required|exists:users,id',  // Ensures the approved_by exists in the users table
        'approved_date' => 'required|date',  // Ensures the approved_date is a valid date
    ];

    public function leaveApplication()
    {
        return $this->belongsTo(LeaveApplication::class, 'leave_application_id');
    }
}
