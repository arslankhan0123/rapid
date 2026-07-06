<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property float $rate
 * @property int|null $tax_1_id
 * @property int|null $tax_2_id
 * @property int $item_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TaxRate|null $firstTax
 * @property-read ProductGroup $group
 * @property-read TaxRate|null $secondTax
 *
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereItemGroupId($value)
 * @method static Builder|Product whereRate($value)
 * @method static Builder|Product whereTax1Id($value)
 * @method static Builder|Product whereTax2Id($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Product extends Model
{
    /**
     * @var string
     */
    protected $table = 'items';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'rate',
        'tax_1_id',
        'tax_2_id',
        'item_group_id',
        'item_number'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|unique:items,title',
        'rate' => 'required',
        'description' => 'nullable',
        'tax_1_id' => 'nullable',
        'tax_2_id' => 'nullable',
        'item_group_id' => 'required',
        'item_number'=>'nullable'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'rate' => 'double',
        'tax_1_id' => 'integer',
        'tax_2_id' => 'integer',
        'item_group_id' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function firstTax(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_1_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function secondTax(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_2_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'item_group_id');
    }

    public function salesItems(){
        return $this->hasMany(SalesItem::class,'service_id');
    }
    public function projects()
    {
        return $this->hasMany(ProjectService::class, 'service_id');
    }

}
