<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $table = 'customer_payments';

    protected $fillable = [
        'customer_id',
        'payment_id',
    ];

    /**
     * Get the customer that owns the payment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the payment associated with the customer.
     */
    public function payment()
    {
        return $this->belongsTo(PaymentMode::class);
    }
}
