<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProductGroup
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 *
 * @method static Builder|ProductGroup newModelQuery()
 * @method static Builder|ProductGroup newQuery()
 * @method static Builder|ProductGroup query()
 * @method static Builder|ProductGroup whereCreatedAt($value)
 * @method static Builder|ProductGroup whereDescription($value)
 * @method static Builder|ProductGroup whereId($value)
 * @method static Builder|ProductGroup whereName($value)
 * @method static Builder|ProductGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ProductGroup extends Model
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
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:item_groups,name',
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
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'item_group_id');
    }
}
