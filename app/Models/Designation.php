<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\SubDepartment;

class Designation extends Model
{
    use HasFactory;

    public static $rules = [
        'name' => [
            'required',
            'unique:designations,name',
            'regex:/^(?!\d+$).*/',
        ],
        'department_id' => 'required',
        'sub_department_id' => 'nullable',
    ];


    /**
     * @var string
     */
    protected $table = 'designations';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'department_id',
        'sub_department_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'created_at' => 'datetime', // Add created_at cast
        'updated_at' => 'datetime', // Add updated_at cast if needed
    ];

    // Define the relationship with the Department model
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Define the relationship with the SubDepartment model
    public function subDepartment()
    {
        return $this->belongsTo(SubDepartment::class, 'sub_department_id');
    }
}
