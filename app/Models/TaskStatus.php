<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    // Specify the table name if it's not the plural form of the model name
    protected $table = 'task_status';

    // Fillable properties
    protected $fillable = [
        'user_id',
        'date',
        'task',
        'description',
        'duration',
        'project_id',
        'project_name',
        'customer_id',
        'branch_id',
        'task_distribution'
    ];

    // Validation rules for storing TaskStatus
    public static function rules()
    {
        return [
            'user_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'duration' => 'required', 
        ];
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    // Relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
