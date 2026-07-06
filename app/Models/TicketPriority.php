<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class TicketPriority
 *
 * @version April 3, 2020, 8:00 am UTC
 *
 * @property string $name
 * @property bool $status
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|TicketPriority newModelQuery()
 * @method static Builder|TicketPriority newQuery()
 * @method static Builder|TicketPriority query()
 * @method static Builder|TicketPriority whereCreatedAt($value)
 * @method static Builder|TicketPriority whereId($value)
 * @method static Builder|TicketPriority whereName($value)
 * @method static Builder|TicketPriority whereStatus($value)
 * @method static Builder|TicketPriority whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TicketPriority extends Model
{
    const STATUS_ALL = 2;

    const ACTIVE = 1;

    const INACTIVE = 0;

    const STATUS_ARR = [
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Deactive',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:ticket_priorities,name',
    ];

    /**
     * @var string
     */
    protected $table = 'ticket_priorities';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'status' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }
}
