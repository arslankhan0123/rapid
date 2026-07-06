<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $table = 'product_colors';

    public static $rules = [
        'title' => 'required|unique:product_colors,title'
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
