<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public static $rules = [
        'name' => [
            'required',
            'unique:states,name',
            'regex:/^[\p{L}\p{M}\s]+$/u',
        ],
        'country_id' => 'required|exists:countries,id'
    ];

    /**
     * @var string
     */
    protected $table = 'states';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'country_id',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];

    /**
     * Get the country that the state belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
