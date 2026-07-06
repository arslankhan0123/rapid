<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersBranch extends Model
{
    use HasFactory;
    // Table name (optional if it follows Laravel naming conventions)
    protected $table = 'users_branches';

    // Fillable properties for mass assignment
    protected $fillable = [
        'user_id',
        'branch_id',
    ];

    /**
     * Validation rules for the model.
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'branch_id' => 'required|integer|exists:branches,id',
        ];
    }

    /**
     * Define the relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the Branch model.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
