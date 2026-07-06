<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Countries
 *
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country query()
 * @mixin Eloquent
 */
class Country extends Model
{
    /**
     * @var string
     */
    protected $table = 'countries';

    /**
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => [
            'required',
            'unique:countries,name',
            'regex:/^[\p{L}\p{M}\s]+$/u',
        ],
    ];
}
