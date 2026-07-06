<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverTime extends Model
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
        'overtime_type_id' => 'required|exists:overtime_types,id',
    ];

    /**
     * @var string
     */
    protected $table = 'overtimes';

    /**
     * @var array
     */
    protected $fillable = [

        'employee_id',
        'amount',
        'overtime_type_id',
        'description'
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
    public function overtimeTypes()
    {
        return $this->BelongsTo(OvertimeType::class, 'overtime_type_id');
    }
}
