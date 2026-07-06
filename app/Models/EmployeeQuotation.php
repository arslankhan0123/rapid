<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeQuotation extends Model
{
    use HasFactory;

    protected $table = 'employee_quotations';

    /**
     * @var array
     */
    protected $fillable = [
        'estimate_id',
        'employee_id',
        'hours',
        'rate',
        'taxes',
        'remarks'
    ];

    public static $rules = [
        'estimate_id' => 'required|integer|exists:estimates,id',
        'employee_id' => 'required|integer|exists:departments,id',
        'hours' => 'required|numeric|min:0',
        'rate' => 'nullable|numeric|min:0',
        'taxes' => 'nullable|numeric|min:0',
        'remarks'=>"nullable"
    ];

    public function employee()
    {
        return $this->belongsTo(Department::class, 'employee_id');
    }
}
