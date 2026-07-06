<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Rules\UniqueHolidayDateRange;
use Illuminate\Support\Facades\Validator;


class Appointment extends Model
{
    use HasFactory;
    protected $table = 'appointments';

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'appointment_date', 
        'appointment_time',
        'appointed_by',
    ];

    // Validation rules
    public static $rules = [
        'name' => 'required|string',
        'mobile' => 'required|string',
        'appointment_date' => 'required|date',
    ];
    
}
