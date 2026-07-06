<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class ArticleGroup
 *
 * @version April 3, 2020, 4:25 am UTC
 *
 * @property int $id
 * @property string group_name
 * @property string color
 * @property string|null $description
 * @property int order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ArticleGroup newModelQuery()
 * @method static Builder|ArticleGroup newQuery()
 * @method static Builder|ArticleGroup query()
 * @method static Builder|ArticleGroup whereColor($value)
 * @method static Builder|ArticleGroup whereCreatedAt($value)
 * @method static Builder|ArticleGroup whereDescription($value)
 * @method static Builder|ArticleGroup whereGroupName($value)
 * @method static Builder|ArticleGroup whereId($value)
 * @method static Builder|ArticleGroup whereOrder($value)
 * @method static Builder|ArticleGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ArticleGroup extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'group_name' => 'required|unique:article_groups,group_name',
        'color' => 'required',
        'order' => 'required',
    ];

    /**
     * @var string
     */
    protected $table = 'article_groups';

    /**
     * @var array
     */
    protected $fillable = [
        'group_name',
        'color',
        'description',
        'order',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'group_name' => 'string',
        'color' => 'string',
        'description' => 'string',
        'order' => 'integer',
    ];

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'group_id');
    }
}
