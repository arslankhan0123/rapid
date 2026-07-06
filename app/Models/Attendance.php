<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances'; // Specify the table name

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'customer_id',
        'project_id',
        'hours'
    ];

    // Define relationships if needed
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function iqmaEmployee(){
        return $this->belongsTo(Employee::class, 'employee_id','iqama_no');
    }

    public static $rules = [
        // 'employee_id' => 'required|exists:employees,id',
        'date' => 'required|date',
        // 'time_in' => 'required|date_format:H:i',
        // 'time_out' => 'nullable|date_format:H:i|after_or_equal:time_in',
    ];

    public static $rules_update = [
        'date' => 'required|date',
        // 'time_in' => 'required',
        // 'time_out' => 'nullable|after_or_equal:time_in',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

}
