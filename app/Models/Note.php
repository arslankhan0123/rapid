<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Note
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Note newModelQuery()
 * @method static Builder|Note newQuery()
 * @method static Builder|Note query()
 * @method static Builder|Note whereCreatedAt($value)
 * @method static Builder|Note whereId($value)
 * @method static Builder|Note whereNote($value)
 * @method static Builder|Note whereOwnerId($value)
 * @method static Builder|Note whereOwnerType($value)
 * @method static Builder|Note whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property int $added_by
 * @property-read User $user
 *
 * @method static Builder|Note whereAddedBy($value)
 */
class Note extends Model
{
    /**
     * @var string
     */
    protected $table = 'notes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'note',
        'added_by',
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
        'note' => 'string',
        'added_by' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
