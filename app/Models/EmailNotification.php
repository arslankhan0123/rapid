<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EmailNotification
 *
 * @method static Builder|EmailNotification newModelQuery()
 * @method static Builder|EmailNotification newQuery()
 * @method static Builder|EmailNotification query()
 * @mixin Eloquent
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|EmailNotification whereCreatedAt($value)
 * @method static Builder|EmailNotification whereId($value)
 * @method static Builder|EmailNotification whereName($value)
 * @method static Builder|EmailNotification whereUpdatedAt($value)
 */
class EmailNotification extends Model
{
    /**
     * @var string
     */
    protected $table = 'email_notifications';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];
}
