<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Increment extends Model
{
    use HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:increments,name',
        'date' => "Required",
        'amount' => 'required|numeric|min:0.01',
    ];

    /**
     * @var string
     */
    protected $table = 'increments';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'date',
        'branch_id',
        'employee_id',
        'amount'
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function employee()
    {
        return $this->BelongsTo(Employee::class, 'employee_id');
    }
}
