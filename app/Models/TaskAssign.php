<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Department;

class TaskAssign extends Model
{
    use HasFactory;
    protected $table = 'task_assign';
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'department_id' => 'nullable|exists:departments,id',
        'designation_id' => 'required|exists:designations,id',
        'employee_id' => 'nullable|exists:employees,id',
    ];

    /**
     * @var string
     */


    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'department_id',
        'designation_id',
        'employee_id'
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
