<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property string $image
 * @property string|null $default_language
 * @property bool $is_enable
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[]
 *     $notifications
 * @property-read int|null $notifications_count
 *
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 *
 * @property string|null $facebook
 * @property string|null $linkedin
 * @property string|null $skype
 * @property bool|null $staff_member
 * @property bool|null $send_welcome_email
 * @property-read Collection|Department[] $departments
 * @property-read int|null $departments_count
 * @property-read string $full_name
 * @property-read int|null $media_count
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder|User permission($permissions)
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereDefaultLanguage($value)
 * @method static Builder|User whereEmailSignature($value)
 * @method static Builder|User whereFacebook($value)
 * @method static Builder|User whereHourlyRate($value)
 * @method static Builder|User whereImage($value)
 * @method static Builder|User whereIsEnable($value)
 * @method static Builder|User whereLinkedin($value)
 * @method static Builder|User whereSendWelcomeEmail($value)
 * @method static Builder|User whereSkype($value)
 * @method static Builder|User whereStaffMember($value)
 *
 * @property int|null $owner_id
 * @property string|null $owner_type
 * @property-read mixed $image_url
 *
 * @method static Builder|User whereOwnerId($value)
 * @method static Builder|User whereOwnerType($value)
 *
 * @property-read Contact|null $contact
 * @property-read Collection|Contact[] $contacts
 * @property-read int|null $contacts_count
 *
 * @method static Builder|User user()
 */
class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory;
    use Notifiable, HasRoles, InteractsWithMedia, Impersonate;

    public const COLLECTION_PROFILE_PICTURES = 'profile';

    const LANGUAGES = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'tr' => 'Turkish',
    ];

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'image',
        'facebook',
        'linkedin',
        'skype',
        'is_enable',
        'staff_member',
        'send_welcome_email',
        'default_language',
        'owner_id',
        'owner_type',
        'email_verified_at',
        'is_admin',
        'employee_id',
        'otp_enabled'
    ];

    const STATUS_ALL = 2;

    const ACTIVE = 1;

    const INACTIVE = 0;

    const STATUS_ARR = [
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Deactive',
    ];

    /**
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $appends = ['full_name', 'image_url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'first_name' => 'string',
        'last_name' => 'string',
        'password' => 'string',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'phone' => 'string',
        'image' => 'string',
        'facebook' => 'string',
        'linkedin' => 'string',
        'skype' => 'string',
        'is_enable' => 'boolean',
        'staff_member' => 'boolean',
        'send_welcome_email' => 'boolean',
        'default_language' => 'string',
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'is_admin' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|regex:/^[\p{L}\p{M}\s]+$/u',
        'phone' => 'required|unique:users,phone',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|same:password_confirmation|min:6',
        'password_confirmation' => 'required',
        'image' => 'nullable|mimes:jpeg,jpg,png|max:2000',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $editRules = [
        'first_name' => 'required|regex:/^[\p{L}\p{M}\s]+$/u',
        'last_name' => 'nullable',
        'phone' => 'required',
        'email' => 'required',
        'image' => 'nullable|mimes:jpeg,jpg,png|max:2000',
    ];

    /**
     * @var array
     */
    public static $messages = [
        'email.regex' => 'Please enter valid email.',
        'password.same' => 'The password and confirm password must match',
        'image.max' => 'Image size should not more than 2MB.',
    ];

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        /** @var Media $media */
        $media = $this->getMedia(self::COLLECTION_PROFILE_PICTURES)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return getUserImageInitial($this->id, $this->full_name);
    }

    /**
     * @return string
     */
    public function getRoleNamesAttribute(): string
    {
        return implode(',', $this->roles->pluck('display_name')->toArray());
    }

    /**
     * @return BelongsToMany
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_departments', 'user_id', 'department_id');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUser(Builder $query): Builder
    {
        return $query->where('owner_type', '!=', Contact::class)->orWhereNull('owner_type');
    }

    /**
     * @return HasOne
     */
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class, 'assigned_user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function goals(): BelongsToMany
    {
        return $this->belongsToMany(Goal::class, 'goal_members');
    }

    /**
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function branches()
    {
        return $this->hasMany(UsersBranch::class, 'user_id', 'id');
    }
}
