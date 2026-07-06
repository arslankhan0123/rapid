<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ContactToPermission
 *
 * @property int $id
 * @property int $contact_id
 * @property int $permission_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ContactToPermission newModelQuery()
 * @method static Builder|ContactToPermission newQuery()
 * @method static Builder|ContactToPermission query()
 * @method static Builder|ContactToPermission whereContactId($value)
 * @method static Builder|ContactToPermission whereCreatedAt($value)
 * @method static Builder|ContactToPermission whereId($value)
 * @method static Builder|ContactToPermission wherePermissionId($value)
 * @method static Builder|ContactToPermission whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ContactToPermission extends Model
{
    /**
     * @var string
     */
    protected $table = 'contact_to_permissions';

    /**
     * @var string[]
     */
    protected $fillable = [
        'contact_id',
        'permission_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'contact_id' => 'integer',
        'permission_id' => 'integer',
    ];
}
