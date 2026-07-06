<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;

class Area extends Model
{
    use HasFactory;
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:areas,name',
        'country_id' => 'required|exists:countries,id',
        'city_id' => 'required|exists:cities,id',
        'state_id' => 'required|exists:states,id',
    ];

    /**
     * @var string
     */
    protected $table = 'areas';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'country_id',
        'city_id',
        'state_id',
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

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the city that the state belongs to.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function state(){
        return $this->belongsTo(State::class, 'state_id');
    }
}
