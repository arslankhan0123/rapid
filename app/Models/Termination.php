<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Termination extends Model
{
    use HasFactory;

    protected $table = 'terminations';
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        // 'name' => 'required',
        'employee_id' => 'required|unique:terminations,employee_id',
        'date' => "required"
    ];


    /**
     * @var string
     */


    /**
     * @var array
     */
    protected $fillable = [
        // 'name',
        'employee_id',
        'description',
        'status',
        'date'
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
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function revoke()
    {
        return $this->hasOne(Revoke::class);
    }

    public function isRevoked()
    {
        return $this->revoke()->exists();
    }
}