<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $table = 'terms';
    // The attributes that are mass assignable
    protected $fillable = ['name', 'terms'];

    // Validation rules
    public static function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'terms' => 'required|string',
        ];
    }
}
