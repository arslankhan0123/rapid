<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EstimateAddress
 *
 * @property int $id
 * @property int $estimate_id
 * @property int $type
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|EstimateAddress newModelQuery()
 * @method static Builder|EstimateAddress newQuery()
 * @method static Builder|EstimateAddress query()
 * @method static Builder|EstimateAddress whereCity($value)
 * @method static Builder|EstimateAddress whereCountry($value)
 * @method static Builder|EstimateAddress whereCreatedAt($value)
 * @method static Builder|EstimateAddress whereEstimateId($value)
 * @method static Builder|EstimateAddress whereId($value)
 * @method static Builder|EstimateAddress whereState($value)
 * @method static Builder|EstimateAddress whereStreet($value)
 * @method static Builder|EstimateAddress whereType($value)
 * @method static Builder|EstimateAddress whereUpdatedAt($value)
 * @method static Builder|EstimateAddress whereZipCode($value)
 * @mixin Eloquent
 */
class EstimateAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'estimate_addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'street',
        'city',
        'state',
        'zip_code',
        'country',
        'type',
        'estimate_id',
        'estimate_number'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'street' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip_code' => 'string',
        'country' => 'string',
        'type' => 'integer',
        'estimate_id' => 'integer',
    ];
}
