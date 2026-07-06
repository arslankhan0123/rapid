<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyAttendanceInvoice extends Model
{
    use HasFactory;

    protected $table = 'monthly_attendance_invoices';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'customer_id',
        'project_id',
        'month',
        'posted_by',
        'updated_by',
        'posted_at',
        'total_employees',
        'total_amount',
        'paid_amount',
        'status',
        'total_hours',
        'vat',
        'discount'
    ];



    /**
     * Define a belongsTo relationship with the User model for the posted_by field.
     */
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Define a belongsTo relationship with the User model for the updated_by field.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    

}
