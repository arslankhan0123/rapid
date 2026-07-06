<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rentals';
    public static $rules = [
        'supplier_id'  => 'required|exists:suppliers,id', // Ensure supplier_id exists in the suppliers table
        'start_date'   => 'required|date',
        'end_date'   => 'required|date',
        'type'         => 'required|in:hourly,daily,monthly',
        'description'  => 'nullable|string',
        'amount' => 'required',
        'tax_id'  => 'required|exists:tax_rates,id',
        'tax_amount' => "required",
        'total_rent_amount' => "required"

    ];

    /**
     * Validation rules
     *
     * @var array
     */

    protected $fillable = [
        'supplier_id',
        'start_date',
        'end_date',
        'type',
        'description',
        'amount',
        'tax_id',
        'tax_amount',
        'total_rent_amount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
