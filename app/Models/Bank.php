<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'branch_name',
        'swift_code',
        'description',
        'opening_balance',
        'iban_number',
        'address'
    ];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:191',
            'account_number' => 'required|string|max:50|unique:banks,account_number',
            'branch_name' => 'nullable|string|max:191',
            'swift_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'opening_balance' => 'required|numeric|min:0.01',
            'iban_number' => 'nullable|string|max:34',
            'address' => 'nullable|string|max:255',
        ];
    }
}
