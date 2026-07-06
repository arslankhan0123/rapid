<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceAddress
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $type
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|InvoiceAddress newModelQuery()
 * @method static Builder|InvoiceAddress newQuery()
 * @method static Builder|InvoiceAddress query()
 * @method static Builder|InvoiceAddress whereCity($value)
 * @method static Builder|InvoiceAddress whereCountry($value)
 * @method static Builder|InvoiceAddress whereCreatedAt($value)
 * @method static Builder|InvoiceAddress whereId($value)
 * @method static Builder|InvoiceAddress whereInvoiceId($value)
 * @method static Builder|InvoiceAddress whereState($value)
 * @method static Builder|InvoiceAddress whereStreet($value)
 * @method static Builder|InvoiceAddress whereType($value)
 * @method static Builder|InvoiceAddress whereUpdatedAt($value)
 * @method static Builder|InvoiceAddress whereZipCode($value)
 * @mixin Eloquent
 */
class InvoiceAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'invoice_addresses';

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
        'invoice_id',
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
        'invoice_id' => 'integer',
    ];
}
