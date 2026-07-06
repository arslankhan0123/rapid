<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatReport extends Model
{
    use HasFactory;

    // Define fillable fields
    // Define the table (optional if using convention)
    protected $table = 'vat_reports';
    protected $fillable = ['period', 'input', 'output', 'net', 'paid', 'unpaid', 'year', 'bank_name', 'account_number', 'branch_id'];

    // Static variables for 'period' enum values


    // Validation rules
    public static $rules = [
        'period' => 'required|in:q1,q2,q3,q4',
        'input' => 'required|numeric|min:0',
        'output' => 'required|numeric|min:0',
        'net' => 'nullable|numeric|min:0',
        'paid' => 'required|numeric|min:0',
        'unpaid' => 'required|numeric|min:0',
        'year' => "required"
    ];

    // Accessor to get period description
    public function getPeriodDescriptionAttribute()
    {
        $periods = [
            'q1' => 'Q1 (Jan - Mar)',
            'q2' => 'Q2 (Apr - Jun)',
            'q3' => 'Q3 (Jul - Sep)',
            'q4' => 'Q4 (Oct - Dec)',
        ];

        return $periods[$this->period] ?? 'Unknown period';
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
