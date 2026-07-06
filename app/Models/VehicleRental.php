<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRental extends Model
{
    use HasFactory;

    protected $table = 'vehicle_rentals';

    protected $fillable = [
        'rental_number',
        'plate_number',
        'name',
        'installment_amount',
        'installment_no',
        'type',
        'amount',
        'agreement_date',
        'expiry_date',
        'notification_date',
        'description',
        'notification_days',
        'branch_id',
        'account_id',
        'paid_amount',
        'agreement_type',
        'installment_date',
    ];

    // Vehicle types as an array
    public static function getVehicleTypes()
    {
        return ['Daily' => 'Daily', 'Weekly' => 'Weekly', 'Monthly' => 'Monthly', 'Yearly' => 'Yearly', 'Twicly' => 'Twicly', 'Quarterly' => 'Quarterly', 'Half Year' => 'Half Year'];
    }

    // Validation rules
    // public static function rules($id = null)
    // {
    //     return [
    //         'rental_number' => 'required|string|max:50|unique:vehicle_rentals,rental_number,' . $id,
    //         'plate_number' => 'nullable|string|max:50|unique:vehicle_rentals,plate_number,' . $id,
    //         'name' => 'required|string|max:100',
    //         'type' => 'nullable|in:Daily,Weekly,Monthly,Yearly,Twicly,Quarterly,Half Year',
    //         'amount' => 'required|numeric|min:0',
    //         'agreement_date' => 'nullable|date',
    //         'expiry_date' => 'required|date|after_or_equal:agreement_date',
    //         'notification_date' => 'nullable|date',
    //         'description' => 'nullable',
    //         'agreement_type' => 'nullable|in:One-time,Installment',
    //     ];
    // }

    public static function rules($id = null)
    {
        return [
            'rental_number' => 'required|string|max:50|unique:vehicle_rentals,rental_number,' . $id,

            // Plate number: still optional but unique
            'plate_number' => 'nullable|string|max:50|unique:vehicle_rentals,plate_number,' . $id,

            // Name: must be letters only (no numbers)
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}\p{M}\s]+$/u', // only letters & spaces
            ],

            // Vehicle type: restricted to defined types
            'type' => 'nullable|in:Daily,Weekly,Monthly,Yearly,Twicly,Quarterly,Half Year',

            // All amounts: must be numeric and at least 0.01
            'amount' => 'required|numeric|min:0.01',
            'installment_amount' => 'nullable|numeric|min:0.01',
            'paid_amount' => 'nullable|numeric|min:0.01',

            // Dates
            'agreement_date' => 'nullable|date',
            'expiry_date' => 'required|date|after_or_equal:agreement_date',
            'notification_date' => 'nullable|date',

            // Description
            'description' => 'nullable|string',

            // Agreement type
            'agreement_type' => 'nullable|in:One-time,Installment',
        ];
    }


    public static function messages()
    {
        return [
            'name.regex' => 'Rental name can only contain letters and spaces.',
            'amount.min' => 'Amount must be at least 0.01.',
            'installment_amount.min' => 'Installment amount must be at least 0.01.',
            'paid_amount.min' => 'Paid amount must be at least 0.01.',
            'expiry_date.after_or_equal' => 'Expiry date cannot be before the agreement date.',
            'plate_number.unique' => 'ID number has already been taken.',
            'plate_number.max' => 'ID number must not be greater than 50 characters.',
            'plate_number.string' => 'ID number must be a string.',
        ];
    }


    // In VehicleRental.php model

    public function setNotificationDaysAttribute($value)
    {
        $this->attributes['notification_days'] = $value ?? 0.00;
    }
}
