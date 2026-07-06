<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;


    // Specify the table name
    protected $table = 'commissions';

    public static $rules =  [
        'employee_id' => 'required|exists:employees,id',
        'commission' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ];

    // Specify which attributes are mass assignable
    protected $fillable = [
        'employee_id',
        'commission',
        'description',
    ];

    // Define the relationship with the Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
