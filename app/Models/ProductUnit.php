<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;
    public static $rules = [
        'title' => 'required|unique:product_units,title'
    ];
    protected $fillable = [
        'title',
        'description'
    ];
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string'
    ];
}
