<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    public static $rules = [
        'title' => [
            'required',
            'unique:asset_categories,title',
            'regex:/^(?![0-9]+$).+$/',
        ],
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
