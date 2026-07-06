<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserNotification
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $type
 * @property string|null $description
 * @property string|null $read_at
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereDescription($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereReadAt($value)
 * @method static Builder|Notification whereTitle($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @method static Builder|Notification whereUserId($value)
 * @mixin Eloquent
 */
class Notification extends Model
{
    /**
     * @var string
     */
    protected $table = 'notifications';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'type',
        'description',
        'read_at',
        'user_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'title' => 'string',
        'type' => 'string',
        'description' => 'string',
        'read_at' => 'datetime',
        'user_id' => 'integer',
    ];
}
