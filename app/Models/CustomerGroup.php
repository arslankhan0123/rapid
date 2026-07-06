<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\CustomerGroup
 *
 * @property int $id
 * @property string $name
 * @property mixed|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|CustomerGroup newModelQuery()
 * @method static Builder|CustomerGroup newQuery()
 * @method static Builder|CustomerGroup query()
 * @method static Builder|CustomerGroup whereCreatedAt($value)
 * @method static Builder|CustomerGroup whereDescription($value)
 * @method static Builder|CustomerGroup whereId($value)
 * @method static Builder|CustomerGroup whereName($value)
 * @method static Builder|CustomerGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CustomerGroup extends Model
{
    /**
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:customer_groups,name',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_groups';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
    ];

    /**
     * @param $value
     * @return string
     */
    public function getDescriptionAttribute($value): string
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * @return HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(CustomerToCustomerGroup::class, 'customer_group_id');
    }
}
