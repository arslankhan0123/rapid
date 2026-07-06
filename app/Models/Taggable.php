<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Taggable
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property int $tag_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Taggable newModelQuery()
 * @method static Builder|Taggable newQuery()
 * @method static Builder|Taggable query()
 * @method static Builder|Taggable whereCreatedAt($value)
 * @method static Builder|Taggable whereId($value)
 * @method static Builder|Taggable whereOwnerId($value)
 * @method static Builder|Taggable whereOwnerType($value)
 * @method static Builder|Taggable whereTagId($value)
 * @method static Builder|Taggable whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property int $taggable_id
 * @property string $taggable_type
 * @property-read Model|Eloquent $owner
 * @property-read Tag $tag
 *
 * @method static Builder|Taggable whereTaggableId($value)
 * @method static Builder|Taggable whereTaggableType($value)
 */
class Taggable extends Model
{
    public $table = 'taggables';

    public $fillable = [
        'owner_id',
        'owner_type',
        'tag_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'tag_id' => 'integer',
    ];

    /**
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
