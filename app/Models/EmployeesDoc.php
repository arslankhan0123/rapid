<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesDoc extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'employees_docs';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'employee_id',
        'file',
        'expiry_date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'employee_id' => 'integer',
        'file' => 'string',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship with Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
