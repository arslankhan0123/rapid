<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $project_name
 * @property int $customer_id
 * @property int|null $calculate_progress_through_tasks
 * @property string|null $progress
 * @property int $billing_type
 * @property int $status
 * @property string|null $estimated_hours
 * @property Carbon $start_date
 * @property Carbon|null $deadline
 * @property string $description
 * @property int $send_email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Customer $customer
 * @property-read Collection|ProjectMember[] $members
 * @property-read int|null $members_count
 * @property-read Collection|User[] $projectContacts
 * @property-read int|null $project_contacts_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 *
 * @method static Builder|Project newModelQuery()
 * @method static Builder|Project newQuery()
 * @method static Builder|Project query()
 * @method static Builder|Project whereBillingType($value)
 * @method static Builder|Project whereCalculateProgressThroughTasks($value)
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereCustomerId($value)
 * @method static Builder|Project whereDeadline($value)
 * @method static Builder|Project whereDescription($value)
 * @method static Builder|Project whereEstimatedHours($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereProgress($value)
 * @method static Builder|Project whereProjectName($value)
 * @method static Builder|Project whereSendEmail($value)
 * @method static Builder|Project whereStartDate($value)
 * @method static Builder|Project whereStatus($value)
 * @method static Builder|Project whereUpdatedAt($value)
 * @mixin Model
 */
class Project extends Model implements \App\Models\Contracts\Taggable
{
    const BILLING_TYPES = [
        '0' => 'Monthly Base ',
        '1' => 'Call Base ',
        '2' => 'Task Hours',
    ];

    const STATUS_BADGE = [
        0 => 'badge-danger',
        1 => 'badge-primary',
        2 => 'badge-warning',
        3 => 'badge-info',
        4 => 'badge-success',
    ];

    const CARD_COLOR = [
        0 => 'danger',
        1 => 'primary',
        2 => 'warning',
        3 => 'info',
        4 => 'success',
    ];

    const STATUS = [
        '3' => 'Cancelled',
        '4' => 'Finished',
        '1' => 'In Progress',
        '0' => 'Not Started',
        '2' => 'On Hold',
    ];

    const STATUS_NOT_STARTED = 0;

    const STATUS_IN_PROGRESS = 1;

    const STATUS_ON_HOLD = 2;

    const STATUS_CANCELLED = 3;

    const STATUS_FINISHED = 4;

    /**
     * @var string
     */
    protected $table = 'projects';

    /**
     * @var string[]
     */
    protected $fillable = [
        'project_name',
        'project_code',
        'calculate_progress_through_tasks',
        'progress',
        'billing_type',
        'status',
        'estimated_hours',
        'start_date',
        'deadline',
        'description',
        'send_email',
        'customer_id',
        'project_location',
        'po_number',
        'branch_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'project_name' => 'string',
        'calculate_progress_through_tasks' => 'integer',
        'progress' => 'string',
        'billing_type' => 'integer',
        'status' => 'integer',
        'estimated_hours' => 'string',
        'start_date' => 'date',
        'deadline' => 'date',
        'description' => 'string',
        'customer_id' => 'integer',
        'send_email' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'project_name' => 'required|unique:projects,project_name',
        'project_code' => 'required|unique:projects,project_code',
        'customer_id' => 'required',
        // 'members' => 'required',
        'branch_id' => 'required',
        'billing_type' => 'required',
        'status' => 'required',
        'start_date' => 'required',
        'project_location' => "nullable",
        'po_number' => "nullable|unique:projects,po_number"
    ];

    /**
     * @var array
     */
    public static $messages = [
        'customer_id.required' => 'Customer field is required.',
    ];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'owner_id');
    }

    /**
     * @return belongsToMany
     */
    public function projectContacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'project_contacts',
            'project_id',
            'contact_id'
        )->withPivot(['contact_id']);
    }

    /**
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOwnerType(): string
    {
        return self::class;
    }

    /**
     * @param  int  $id
     * @return string
     */
    public static function getBillingTypeText($id): string
    {
        return self::BILLING_TYPES[$id];
    }

    /**
     * @param  int  $id
     * @return string
     */
    public static function getStatusText($id): string
    {
        return self::STATUS[$id];
    }

    /**
     * @param $value
     * @return string
     */
    public function getDescriptionAttribute($value): string
    {
        return $this->attributes['description'] = htmlspecialchars_decode($value);
    }
    public function services()
    {
        return $this->hasMany(ProjectService::class, 'project_id');
    }
    public function projectServices()
    {
        return $this->hasMany(ProjectService::class, 'project_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }
    public function terms()
    {
        return $this->hasMany(ProjectTerm::class, 'project_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
