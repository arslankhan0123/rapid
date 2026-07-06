<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Bank;

class Check extends Model
{
    use HasFactory;

    // Table name (if different from the plural form of the model name)
    protected $table = 'checks';

    // The attributes that are mass assignable
    protected $fillable = [
        'check_number',
        'issue_name',
        'amount',
        'branch_id',
        'date',
        'bank_id',
        'issue_place',
    ];

    // Validation rules for the Check model
    public static $rules = [
        'check_number' => 'required|string|max:255|unique:checks,check_number',
        'issue_name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'date' => 'required|date',
        'branch_id' => [
            'required',
            'exists:branches,id', // Ensures that the branch_id exists in the branches table
        ],
        'bank_id' => 'required',
    ];

    // Relationship: Check belongs to Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->format('d-m-Y');
    }
    public function Bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
