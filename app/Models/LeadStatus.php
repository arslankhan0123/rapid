<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class LeadStatus
 *
 * @version April 6, 2020, 4:03 am UTC
 *
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|LeadStatus newModelQuery()
 * @method static Builder|LeadStatus newQuery()
 * @method static Builder|LeadStatus query()
 * @mixin Eloquent
 *
 * @method static Builder|LeadStatus whereColor($value)
 * @method static Builder|LeadStatus whereCreatedAt($value)
 * @method static Builder|LeadStatus whereId($value)
 * @method static Builder|LeadStatus whereName($value)
 * @method static Builder|LeadStatus whereOrder($value)
 * @method static Builder|LeadStatus whereUpdatedAt($value)
 */
class LeadStatus extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:lead_statuses,name',
        'order' => 'required',
    ];

    /**
     * @var string
     */
    protected $table = 'lead_statuses';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'color',
        'order',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'color' => 'string',
        'order' => 'integer',
    ];

    /**
     * @return HasMany
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'status_id');
    }
}
