<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_accounts';

    public static $rules = [
        'name' => 'required|string|max:255',
        'account_number' => 'required|string|max:100|unique:master_accounts,account_number',
        'account_level' => 'required|in:level-1,level-2,level-3,level-4',
        'account_type' => 'nullable|in:Assets,Liabilities,Equity',
        'report_type' => 'required|in:Profit and Loss Account,Balance Sheet,Trading Account',
        'amount_type' => 'required|in:Debit,Credit',
        'description' => 'nullable|string',
        'status' => 'boolean'
    ];

    protected $fillable = [
        'name',
        'account_number',
        'account_level',
        'account_type',
        'report_type',
        'amount_type',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Helper methods
    public function getAccountLevelLabelAttribute()
    {
        $levels = [
            'level-1' => 'Level 1',
            'level-2' => 'Level 2',
            'level-3' => 'Level 3',
            'level-4' => 'Level 4'
        ];

        return $levels[$this->account_level] ?? $this->account_level;
    }

    public function getAccountTypeLabelAttribute()
    {
        return $this->account_type;
    }

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
