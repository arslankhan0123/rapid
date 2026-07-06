<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProjectMember
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|ProjectMember newModelQuery()
 * @method static Builder|ProjectMember newQuery()
 * @method static Builder|ProjectMember query()
 * @method static Builder|ProjectMember whereCreatedAt($value)
 * @method static Builder|ProjectMember whereId($value)
 * @method static Builder|ProjectMember whereOwnerId($value)
 * @method static Builder|ProjectMember whereOwnerType($value)
 * @method static Builder|ProjectMember whereUpdatedAt($value)
 * @method static Builder|ProjectMember whereUserId($value)
 * @mixin Eloquent
 */
class ProjectMember extends Model
{
    /**
     * @var string
     */
    protected $table = 'project_members';

    /**
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'user_id',
        'hourly_rate'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'user_id' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'user_id');
    }
    public function project(){
        return $this->belongsTo(Project::class, 'owner_id');
    }
}
