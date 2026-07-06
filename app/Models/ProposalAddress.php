<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProposalAddress
 *
 * @property int $id
 * @property int $proposal_id
 * @property int $type
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ProposalAddress newModelQuery()
 * @method static Builder|ProposalAddress newQuery()
 * @method static Builder|ProposalAddress query()
 * @method static Builder|ProposalAddress whereCity($value)
 * @method static Builder|ProposalAddress whereCountry($value)
 * @method static Builder|ProposalAddress whereCreatedAt($value)
 * @method static Builder|ProposalAddress whereId($value)
 * @method static Builder|ProposalAddress whereProposalId($value)
 * @method static Builder|ProposalAddress whereState($value)
 * @method static Builder|ProposalAddress whereStreet($value)
 * @method static Builder|ProposalAddress whereType($value)
 * @method static Builder|ProposalAddress whereUpdatedAt($value)
 * @method static Builder|ProposalAddress whereZipCode($value)
 * @mixin Eloquent
 */
class ProposalAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'proposal_addresses';

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
        'proposal_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'street' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip_code' => 'string',
        'country' => 'string',
        'type' => 'integer',
        'proposal_id' => 'integer',
    ];
}
