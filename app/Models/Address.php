<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Address
 *
 * @property-read Model|Eloquent $owner
 *
 * @method static Builder|Address newModelQuery()
 * @method static Builder|Address newQuery()
 * @method static Builder|Address query()
 * @mixin Eloquent
 *
 * @property int $id
 * @property int|null $owner_id
 * @property string|null $owner_type
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property string|null $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Address whereCity($value)
 * @method static Builder|Address whereCountry($value)
 * @method static Builder|Address whereCreatedAt($value)
 * @method static Builder|Address whereId($value)
 * @method static Builder|Address whereOwnerId($value)
 * @method static Builder|Address whereOwnerType($value)
 * @method static Builder|Address whereState($value)
 * @method static Builder|Address whereStreet($value)
 * @method static Builder|Address whereType($value)
 * @method static Builder|Address whereUpdatedAt($value)
 * @method static Builder|Address whereZip($value)
 */
class Address extends Model
{
    const ADDRESS_TYPES = [
        '0' => 'Billing/Shipping',
        '1' => 'Billing Address',
        '2' => 'Shipping Address',
    ];

    /**
     * @var string
     */
    protected $table = 'addresses';

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'street',
        'city',
        'state',
        'zip',
        'country',
        'type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'street' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip' => 'string',
        'country' => 'string',
        'type' => 'string',
    ];

    /**
     * @param  array  $input
     * @return array
     */
    public static function prepareAddressArray($input): array
    {
        $address = [
            'street' => $input['street'],
            'city' => $input['address_city'],
            'zip' => $input['address_zip'],
            'state' => $input['state'],
            'country' => $input['address_country'],
        ];

        return $address;
    }

    /**
     * @param  array  $input
     * @return array
     */
    public static function prepareInputForAddress($input): array
    {
        $items = [];
        foreach ($input as $key => $data) {
            foreach ($data as $index => $value) {
                $items[$index][$key] = $value;
            }
        }

        return $items;
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public static function containsOnlyNull($input): bool
    {
        return empty(array_filter($input, function ($key) {
            return $key !== null;
        }));
    }

    /**
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function addressCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }
    public function customerState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state');
    }
}
