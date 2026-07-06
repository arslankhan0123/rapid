<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class TicketStatus
 *
 * @version April 4, 2020, 3:50 am UTC
 *
 * @property string $name
 * @property string pick_color
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|TicketStatus newModelQuery()
 * @method static Builder|TicketStatus newQuery()
 * @method static Builder|TicketStatus query()
 * @method static Builder|TicketStatus whereCreatedAt($value)
 * @method static Builder|TicketStatus whereId($value)
 * @method static Builder|TicketStatus wherePickColor($value)
 * @method static Builder|TicketStatus whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property int|null $is_default
 *
 * @method static Builder|TicketStatus whereIsDefault($value)
 * @method static Builder|TicketStatus whereName($value)
 */
class TicketStatus extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:ticket_statuses,name',
    ];

    /**
     * @var string
     */
    protected $table = 'ticket_statuses';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'pick_color',
        'is_default',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'pick_color' => 'string',
        'is_default' => 'integer',
    ];

    /**
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'ticket_status_id');
    }
}
