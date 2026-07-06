<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'salary_payment';

    // Mass assignable attributes
    protected $fillable = [
        'salary_sheet_id',
        'payment_type',
        'bank_id',
        'amount',
    ];

    // Validation rules
    public static function rules()
    {
        return [
            'salary_sheet_id' => 'required|exists:salary_sheets,id',
            'payment_type'    => 'required|in:cash,bank',
            'bank_id'         => 'nullable|exists:banks,id',
            'amount'          => 'required|numeric|min:0',
        ];
    }

    // Relationships

    /**
     * Get the salary sheet associated with the payment.
     */
    public function salarySheet()
    {
        return $this->belongsTo(SalarySheet::class, 'salary_sheet_id');
    }

    /**
     * Get the bank associated with the payment.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
