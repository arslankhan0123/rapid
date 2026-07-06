<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'from_account',
        'to_account',
        'transfer_amount',
        'branch_id'
    ];

    public static function rules()
    {
        return [
            'transfer_id' => "required|Unique:cash_transfers,transfer_id",
            'from_account' => 'required|exists:accounts,id|different:to_account',
            'to_account' => 'required|exists:accounts,id|different:from_account',
            'transfer_amount' => 'required|numeric|min:0.01',
            'branch_id' => "required"
        ];
    }
    // Define relationships with the Account model
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
