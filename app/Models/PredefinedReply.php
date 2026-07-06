<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class PredefinedReply
 *
 * @version April 3, 2020, 4:54 am UTC
 *
 * @property string reply_name
 * @property string|null body
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static bool|null forceDelete()
 * @method static Builder|PredefinedReply newModelQuery()
 * @method static Builder|PredefinedReply newQuery()
 * @method static \Illuminate\Database\Query\Builder|PredefinedReply onlyTrashed()
 * @method static Builder|PredefinedReply query()
 * @method static bool|null restore()
 * @method static Builder|PredefinedReply whereBody($value)
 * @method static Builder|PredefinedReply whereCreatedAt($value)
 * @method static Builder|PredefinedReply whereDeletedAt($value)
 * @method static Builder|PredefinedReply whereId($value)
 * @method static Builder|PredefinedReply whereReplyName($value)
 * @method static Builder|PredefinedReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PredefinedReply withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PredefinedReply withoutTrashed()
 * @mixin Eloquent
 */
class PredefinedReply extends Model
{
    /**
     * @var string
     */
    protected $table = 'predefined_replies';

    /**
     * @var string[]
     */
    protected $fillable = [
        'reply_name',
        'body',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'reply_name' => 'string',
        'body' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'reply_name' => 'required|unique:predefined_replies,reply_name',
    ];
}
