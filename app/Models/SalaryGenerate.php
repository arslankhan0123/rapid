<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\SalarySheet;

class SalaryGenerate extends Model
{
    use HasFactory;
    // Specify the table name if it's not the plural form of the model name
    protected $table = 'salary_generates';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'salary_month',
        'generate_date',
        'generated_by',
        'approved_by',
        'status',
        'approved_date',
        'amount',
        'branch_id',
    ];

    /**
     * Get the employee who generated the salary.
     */
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the employee who approved the salary.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Define the validation rules.
     *
     * @param int|null $ignoreId
     * @return array
     */
    public static $rules = [
        'salary_month' => 'required|string|size:7',
        'branch_id' => 'required|exists:branches,id',
        'generate_date' => 'nullable|date',
        'generated_by' => 'nullable|exists:employees,id',
        'approved_by' => 'nullable|exists:employees,id',
        'approved_date' => 'nullable|date|after_or_equal:generate_date',
    ];
    public function salarySheets()
    {
        return $this->hasMany(SalarySheet::class, 'salary_generate_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
