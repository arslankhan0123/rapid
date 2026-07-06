<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyLoan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_loans';

    protected $fillable = [
        'date',
        'type',
        'name',
        'loan_amount',
        'loan_received_date',
        'loan_refund_date',
        'number_of_days',
        'customer_id',
        'supplier_id'
    ];

    protected $casts = [
        'date' => 'date',
        'loan_received_date' => 'date',
        'loan_refund_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
