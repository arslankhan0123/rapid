<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Item
 *
 * @version April 7, 2020, 4:28 am UTC
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
 * @property-read ItemGroup $group
 * @property-read TaxRate|null $secondTax
 *
 * @method static Builder|Item newModelQuery()
 * @method static Builder|Item newQuery()
 * @method static Builder|Item query()
 * @method static Builder|Item whereCreatedAt($value)
 * @method static Builder|Item whereDescription($value)
 * @method static Builder|Item whereId($value)
 * @method static Builder|Item whereItemGroupId($value)
 * @method static Builder|Item whereRate($value)
 * @method static Builder|Item whereTax1Id($value)
 * @method static Builder|Item whereTax2Id($value)
 * @method static Builder|Item whereTitle($value)
 * @method static Builder|Item whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Item extends Model
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
        return $this->belongsTo(ItemGroup::class, 'item_group_id');
    }
}
