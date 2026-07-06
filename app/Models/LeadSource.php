<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class LeadSource
 *
 * @version April 6, 2020, 5:43 am UTC
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|LeadSource newModelQuery()
 * @method static Builder|LeadSource newQuery()
 * @method static Builder|LeadSource query()
 * @method static Builder|LeadSource whereCreatedAt($value)
 * @method static Builder|LeadSource whereId($value)
 * @method static Builder|LeadSource whereName($value)
 * @method static Builder|LeadSource whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LeadSource extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:lead_sources,name',
    ];

    /**
     * @var string
     */
    protected $table = 'lead_sources';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
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
     * @return HasOne
     */
    public function usedLeadSource(): HasOne
    {
        return $this->hasOne(Lead::class, 'source_id');
    }
}
