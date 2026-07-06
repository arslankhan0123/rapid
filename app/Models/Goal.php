<?php

namespace App\Models;

use App\Repositories\GoalRepository;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Goal
 *
 * @property int $id
 * @property string $subject
 * @property string|null $description
 * @property int $goal_type
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property string|null $achievement
 * @property bool|null $is_notify
 * @property bool|null $is_not_notify
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $goalMembers
 * @property-read int|null $goal_members_count
 *
 * @method static Builder|Goal newModelQuery()
 * @method static Builder|Goal newQuery()
 * @method static Builder|Goal query()
 * @method static Builder|Goal whereAchievement($value)
 * @method static Builder|Goal whereCreatedAt($value)
 * @method static Builder|Goal whereDescription($value)
 * @method static Builder|Goal whereEndDate($value)
 * @method static Builder|Goal whereGoalType($value)
 * @method static Builder|Goal whereId($value)
 * @method static Builder|Goal whereIsNotNotify($value)
 * @method static Builder|Goal whereIsNotify($value)
 * @method static Builder|Goal whereStartDate($value)
 * @method static Builder|Goal whereSubject($value)
 * @method static Builder|Goal whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property-read mixed $goal_progress_count
 */
class Goal extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|unique:goals,subject',
        'users' => 'required',
        'achievement' => 'required|numeric|min:1',
        'start_date' => 'required',
        'end_date' => 'required',
    ];

    /**
     * @var array
     */
    public static $messages = [
        'subject.required' => 'Subject field is required.',
        'users.required' => 'Staff member field is required.',
        'achievement.required' => 'Achievement field is required.',
        'start_date.required' => 'Start date field is required.',
        'end_date.required' => 'End date field is required.',
    ];

    /**
     * @var string
     */
    protected $table = 'goals';

    /**
     * @var string[]
     */
    protected $appends = ['goal_progress_count'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'subject',
        'achievement',
        'description',
        'goal_type',
        'is_notify',
        'is_not_notify',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'subject' => 'string',
        'description' => 'string',
        'goal_type' => 'integer',
        'is_notify' => 'boolean',
        'is_not_notify' => 'boolean',
        'achievement' => 'double',
    ];

    const INVOICE_AMOUNT = 2;

    const CONVERT_X_LEAD = 3;

    const INCREASE_CUSTOMER_NUMBER = 4;

    const GOAL_TYPE = [
        self::CONVERT_X_LEAD => 'Convert X Lead',
        self::INVOICE_AMOUNT => 'Invoice Amount',
        self::INCREASE_CUSTOMER_NUMBER => 'Increase Customer Number',
    ];

    /**
     * @return belongsToMany
     */
    public function goalMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'goal_members');
    }

    /**
     * @return string|void
     */
    public function getGoalProgressCountAttribute()
    {
        $data['goal_ype'] = $this->goal_type;
        $data['start_date'] = Carbon::parse($this->start_date)->format('Y-m-d');
        $data['end_date'] = Carbon::parse($this->end_date)->format('Y-m-d');
        $data['achievement'] = $this->achievement;

        /** @var GoalRepository $goalRepo */
        $goalRepo = app(GoalRepository::class);
        $goalProgress = $goalRepo->countGoalProgress($data);

        return $goalProgress;
    }
}
