<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Announcement
 *
 * @version April 6, 2020, 6:50 am UTC
 *
 * @property int $id
 * @property string subject
 * @property Carbon $date
 * @property string message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Announcement newModelQuery()
 * @method static Builder|Announcement newQuery()
 * @method static Builder|Announcement query()
 * @method static Builder|Announcement whereCreatedAt($value)
 * @method static Builder|Announcement whereId($value)
 * @method static Builder|Announcement whereMessage($value)
 * @method static Builder|Announcement whereSubject($value)
 * @method static Builder|Announcement whereDate($value)
 * @method static Builder|Announcement whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property bool|null $show_to_clients
 *
 * @method static Builder|Announcement whereShowToClients($value)
 *
 * @property bool $status
 *
 * @method static Builder|Announcement whereStatus($value)
 */
class Announcement extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|unique:announcements,subject',
    ];

    /**
     * @var string
     */
    protected $table = 'announcements';

    /**
     * @var string[]
     */
    protected $fillable = [
        'subject',
        'date',
        'message',
        'show_to_clients',
        'status',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'subject' => 'string',
        'date' => 'datetime',
        'message' => 'string',
        'show_to_clients' => 'boolean',
        'status' => 'boolean',
    ];

    const PENDING = 0;

    const COMPLETED = 1;

    const STATUS_ARRAY = [
        self::PENDING => 'Pending',
        self::COMPLETED => 'Completed',
    ];

    const ACTIVE = 1;

    const DEACTIVE = 0;

    const SHOW_TO_CLIENT_ARRAY = [
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];
}
