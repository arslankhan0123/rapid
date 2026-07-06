<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    protected $fillable = [
        'account_number',
        'account_name',
        'opening_balance',
        'branch_id',
        'received_by',
        'current_balance',
        'date'
    ];

    // Validation rules
    public static $rules = [
        'account_number' => 'required|max:20', // Ensure it's unique, alphanumeric, and has a max length
        'account_name' => 'required|string|max:255', // Ensure account name is a string with a max length
        'opening_balance' => 'required|numeric|min:0', // Ensure opening balance is a positive number
        'branch_id' => "required",
        'received_by' => "required"
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function receivedBy()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }
    public function from()
    {
        return $this->hasMany(CashTransfer::class, 'from_account', 'id');
    }
    public function to()
    {
        return $this->hasMany(CashTransfer::class, 'to_account', 'id');
    }
}
