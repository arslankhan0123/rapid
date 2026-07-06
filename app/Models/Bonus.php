<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BonusType;

class Bonus extends Model
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
        'bonus_type_id' => 'required|exists:bonus_types,id',
        'date'=>"required"
    ];

    /**
     * @var string
     */
    protected $table = 'bonuses';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'amount',
        'bonus_type_id',
        'description',
        'date',
        'posted'
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
    public function bonusTypes()
    {
        return $this->BelongsTo(BonusType::class, 'bonus_type_id');
    }
}
