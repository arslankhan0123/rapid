<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSubCategory extends Model
{
    use HasFactory;
    protected $table = 'expense_sub_categories';
    protected $fillable = [
        'expense_category_id',
        'name',
        'description',
    ];

    public static $rules = [
        'expense_category_id' => 'required|exists:expense_categories,id|integer',
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[\p{L}\p{M}\s]+$/u',
        ],
        'description' => 'nullable|string',
    ];


    // Define relationship with ExpenseCategory model
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
