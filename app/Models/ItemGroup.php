<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class ItemGroup
 *
 * @version April 6, 2020, 5:56 am UTC
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ItemGroup newModelQuery()
 * @method static Builder|ItemGroup newQuery()
 * @method static Builder|ItemGroup query()
 * @method static Builder|ItemGroup whereCreatedAt($value)
 * @method static Builder|ItemGroup whereDescription($value)
 * @method static Builder|ItemGroup whereId($value)
 * @method static Builder|ItemGroup whereName($value)
 * @method static Builder|ItemGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ItemGroup extends Model
{
    /**
     * @var string
     */
    protected $table = 'item_groups';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:item_groups,name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'item_group_id');
    }
}
