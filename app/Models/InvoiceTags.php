<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceTags
 *
 * @property int $id
 * @property int $taggable_id
 * @property string $taggable_type
 * @property int $tag_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|InvoiceTags newModelQuery()
 * @method static Builder|InvoiceTags newQuery()
 * @method static Builder|InvoiceTags query()
 * @method static Builder|InvoiceTags whereCreatedAt($value)
 * @method static Builder|InvoiceTags whereId($value)
 * @method static Builder|InvoiceTags whereTagId($value)
 * @method static Builder|InvoiceTags whereTaggableId($value)
 * @method static Builder|InvoiceTags whereTaggableType($value)
 * @method static Builder|InvoiceTags whereUpdatedAt($value)
 * @mixin Eloquent
 */
class InvoiceTags extends Model
{
    /**
     * @var string
     */
    protected $table = 'taggables';

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_type',
        'owner_id',
        'tag_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'owner_type' => 'string',
        'owner_id' => 'integer',
        'tag_id' => 'integer',
    ];
}
