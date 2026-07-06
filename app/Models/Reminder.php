<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Reminder
 *
 * @version April 15, 2020, 9:24 am UTC
 *
 * @property string|\Carbon\Carbon notified_date
 * @property int reminder_to
 * @property string description
 * @property bool|null $is_notified
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|Reminder newModelQuery()
 * @method static Builder|Reminder newQuery()
 * @method static Builder|Reminder query()
 * @method static Builder|Reminder whereCreatedAt($value)
 * @method static Builder|Reminder whereDescription($value)
 * @method static Builder|Reminder whereId($value)
 * @method static Builder|Reminder whereIsNotified($value)
 * @method static Builder|Reminder whereNotifiedDate($value)
 * @method static Builder|Reminder whereReminderTo($value)
 * @method static Builder|Reminder whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property int $owner_id
 * @property string $owner_type
 *
 * @method static Builder|Reminder whereOwnerId($value)
 * @method static Builder|Reminder whereOwnerType($value)
 *
 * @property int $added_by
 *
 * @method static Builder|Reminder whereAddedBy($value)
 *
 * @property-read Contact $contact
 * @property bool $status
 *
 * @method static Builder|Announcement whereStatus($value)
 */
class Reminder extends Model
{
    const REMINDER_MODULES = [
        1 => Ticket::class,
        2 => Task::class,
        3 => Invoice::class,
        4 => Expense::class,
        5 => Customer::class,
        6 => Lead::class,
        7 => Proposal::class,
    ];

    const IS_NOTIFIED = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    const PENDING = 0;

    const COMPLETED = 1;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'notified_date' => 'required',
        'reminder_to' => 'required',
        'description' => 'required',
    ];

    /**
     * @var string
     */
    protected $table = 'reminders';

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'notified_date',
        'reminder_to',
        'description',
        'is_notified',
        'added_by',
        'status',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'reminder_to' => 'integer',
        'notified_date' => 'datetime',
        'description' => 'string',
        'is_notified' => 'boolean',
        'added_by' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reminder_to');
    }

    /**
     * @return BelongsTo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'user_id');
    }
}
