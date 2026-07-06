<?php

namespace App\Models;

use App\Models\Contracts\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Ticket
 *
 * @version April 8, 2020, 6:13 am UTC
 *
 * @property string $subject
 * @property int|null $contact_id
 * @property string $name
 * @property string|null $email
 * @property int|null $department_id
 * @property string|null $cc
 * @property int|null $assign_to
 * @property int $priority_id
 * @property int $service_id
 * @property int|null $predefined_reply_id
 * @property string attachments
 * @property int $id
 * @property string|null $body
 * @property int|null $ticket_status_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PredefinedReply|null $predefinedReply
 * @property-read Service $service
 * @property-read User|null $user
 *
 * @method static Builder|Ticket newModelQuery()
 * @method static Builder|Ticket newQuery()
 * @method static Builder|Ticket query()
 * @method static Builder|Ticket whereAssignTo($value)
 * @method static Builder|Ticket whereBody($value)
 * @method static Builder|Ticket whereCc($value)
 * @method static Builder|Ticket whereContactId($value)
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereDepartmentId($value)
 * @method static Builder|Ticket whereEmail($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket whereName($value)
 * @method static Builder|Ticket wherePredefinedReplyId($value)
 * @method static Builder|Ticket wherePriorityId($value)
 * @method static Builder|Ticket whereServiceId($value)
 * @method static Builder|Ticket whereStatus($value)
 * @method static Builder|Ticket whereSubject($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 *
 * @property-read int|null $media_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Department $department
 * @property-read TicketPriority $ticketPriority
 * @property-read TicketStatus $ticketStatus
 *
 * @method static Builder|Ticket whereTicketStatusId($value)
 *
 * @property-read Contact|null $contact
 * @property-read bool|string $ticket_attachments
 */
class Ticket extends Model implements HasMedia, Taggable
{
    use InteractsWithMedia;

    public const TICKET_ATTACHMENT_PATH = 'tickets';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|unique:tickets,subject',
        'email' => 'nullable|email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
        'cc' => 'nullable|email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
        'attachments' => 'nullable|max:2000',
    ];

    public static $messages = [
        'attachments.max' => 'Attachment size should not more than 2MB.',
    ];

    /**
     * @var string
     */
    protected $table = 'tickets';

    /**
     * @var string[]
     */
    protected $fillable = [
        'subject',
        'contact_id',
        'name',
        'email',
        'department_id',
        'cc',
        'assign_to',
        'priority_id',
        'service_id',
        'predefined_reply_id',
        'body',
        'ticket_status_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'subject' => 'string',
        'contact_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'department_id' => 'integer',
        'cc' => 'string',
        'assign_to' => 'integer',
        'priority_id' => 'integer',
        'service_id' => 'integer',
        'predefined_reply_id' => 'integer',
        'body' => 'string',
        'ticket_status_id' => 'integer',
    ];

    /**
     * @var array
     */
    protected $appends = ['ticket_attachments'];

    /**
     * @return bool|string
     */
    public function getTicketAttachmentsAttribute()
    {
        /** @var Media $media */
        $media = $this->getMedia(self::TICKET_ATTACHMENT_PATH)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return false;
    }

    /**
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * @return BelongsTo
     */
    public function ticketPriority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    /**
     * @return BelongsTo
     */
    public function ticketStatus(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_status_id');
    }

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    /**
     * @return BelongsTo
     */
    public function predefinedReply(): BelongsTo
    {
        return $this->belongsTo(PredefinedReply::class, 'predefined_reply_id');
    }

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
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
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
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'owner_id');
    }

    /**
     * @return belongsToMany
     */
    public function ticketReplies(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_replies')
            ->orderByPivot('created_at', 'desc')->withPivot(['id', 'reply', 'created_at']);
    }
}
