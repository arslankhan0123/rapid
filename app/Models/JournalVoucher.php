<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'description',
        'branch_id',
        'from_account'
    ];

    // Validation rules for creating/updating Journal Voucher
    public static $rules = [
        'account_id' => 'required|exists:accounts,id', // account_id must exist in the accounts table
        'amount' => 'required|numeric|min:0.01', // amount should be numeric and at least 0.01
        'description' => 'nullable', // notes are optional but should not exceed 255 characters
        'branch_id' => "required"
    ];

    // Define relationship with the Account model (assuming you have an 'accounts' table)
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account');
    }
}
