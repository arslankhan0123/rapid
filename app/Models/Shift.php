<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;
    // Specify the table associated with the model
    protected $table = 'shifts';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'name',
        'shift_start_time',
        'shift_end_time',
        'lunch_start_time',
        'lunch_end_time',
        'color',
        'description',
    ];

    // If you are using timestamps, this will enable the 'created_at' and 'updated_at' columns
    public $timestamps = true;

    // Optionally, specify the date format
    protected $dateFormat = 'Y-m-d H:i:s';

    // Optionally, specify the casts for date/time attributes
    protected $casts = [
        'shift_start_time' => 'datetime:H:i:s',
        'shift_end_time' => 'datetime:H:i:s',
        'lunch_start_time' => 'datetime:H:i:s',
        'lunch_end_time' => 'datetime:H:i:s',
    ];

    public static $rules = [
        'name' => 'required|string|max:255|unique:shifts,name',
        'shift_start_time' => 'required|date_format:H:i',
        'shift_end_time' => 'required|date_format:H:i|after:shift_start_time',
        'lunch_start_time' => 'nullable|date_format:H:i|after:shift_start_time',
        'lunch_end_time' => 'nullable|date_format:H:i|after:lunch_start_time',
        'color' => 'required|string|max:7', // Assuming a hex color code
        'description' => 'nullable|string',
    ];


}
