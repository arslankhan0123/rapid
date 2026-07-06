<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * Class Task
 *
 * @version April 13, 2020, 10:21 am UTC
 *
 * @property int $id
 * @property int|null $public
 * @property int|null $billable
 * @property string $subject
 * @property int $status
 * @property string|null $hourly_rate
 * @property string $start_date
 * @property string|null $end_date
 * @property int|null $priority
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task query()
 * @method static Builder|Task whereBillable($value)
 * @method static Builder|Task whereCreatedAt($value)
 * @method static Builder|Task whereDescription($value)
 * @method static Builder|Task whereEndDate($value)
 * @method static Builder|Task whereHourlyRate($value)
 * @method static Builder|Task whereId($value)
 * @method static Builder|Task wherePriority($value)
 * @method static Builder|Task wherePublic($value)
 * @method static Builder|Task whereStartDate($value)
 * @method static Builder|Task whereStatus($value)
 * @method static Builder|Task whereSubject($value)
 * @method static Builder|Task whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property string|null $due_date
 * @property int|null $related_to
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 *
 * @method static Builder|Task whereDueDate($value)
 * @method static Builder|Task whereOwnerId($value)
 * @method static Builder|Task whereOwnerType($value)
 * @method static Builder|Task whereRelatedTo($value)
 *
 * @property int|null $member_id
 * @property-read Collection|User[] $user
 * @property-read int|null $user_count
 *
 * @method static Builder|Task whereMemberId($value)
 */
class Task extends Model
{
    const NOT_STARTED_STATUS = 1;

    const STATUS = [
        4 => 'Awaiting Feedback',
        5 => 'Completed',
        2 => 'In Progress',
        1 => 'Not Started',
        3 => 'Testing',
    ];

    const PRIORITY = [
        3 => 'High',
        1 => 'Low',
        2 => 'Medium',
        4 => 'Urgent',
    ];

    const RELATED_TO = [
        1 => Invoice::class,
        2 => Customer::class,
        3 => Ticket::class,
        4 => Project::class,
        5 => Proposal::class,
        6 => Estimate::class,
        7 => Lead::class,
        8 => Contract::class,
    ];

    const RELATED_TO_array = [
        1 => 'Invoice',
        2 => 'Customer',
        3 => 'Ticket',
        4 => 'Project',
        5 => 'Proposal',
        6 => 'Estimate',
        7 => 'Lead',
        8 => 'Contract',
    ];

    /**
     * @var string
     */
    protected $table = 'tasks';

    /**
     * @var string[]
     */
    protected $fillable = [
        'public',
        'billable',
        'subject',
        'status',
        'hourly_rate',
        'start_date',
        'due_date',
        'priority',
        'description',
        'related_to',
        'owner_type',
        'owner_id',
        'member_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'public' => 'boolean',
        'billable' => 'boolean',
        'subject' => 'string',
        'status' => 'integer',
        'hourly_rate' => 'string',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'priority' => 'integer',
        'description' => 'string',
        'related_to' => 'integer',
        'owner_type' => 'string',
        'owner_id' => 'integer',
        'member_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|unique:tasks,subject',
        'status' => 'required',
    ];

    /**
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'owner_id');
    }

    /**
     * @param  string  $ownerType
     * @param  string  $ownerFieldName
     * @return mixed
     */
    public function getRelatedTo($ownerType, $ownerFieldName)
    {
        return $this->belongsTo($ownerType, 'owner_id')->value($ownerFieldName);
    }
}
