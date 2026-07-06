<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PurchaseItem;

/**
 * App\Models\SalesItem
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string $item
 * @property string|null $description
 * @property int $quantity
 * @property float $rate
 * @property float $total
 * @property string $tax_ids
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|SalesItem newModelQuery()
 * @method static Builder|SalesItem newQuery()
 * @method static Builder|SalesItem query()
 * @method static Builder|SalesItem whereCreatedAt($value)
 * @method static Builder|SalesItem whereDescription($value)
 * @method static Builder|SalesItem whereId($value)
 * @method static Builder|SalesItem whereItem($value)
 * @method static Builder|SalesItem whereOwnerId($value)
 * @method static Builder|SalesItem whereOwnerType($value)
 * @method static Builder|SalesItem whereQuantity($value)
 * @method static Builder|SalesItem whereRate($value)
 * @method static Builder|SalesItem whereTaxIds($value)
 * @method static Builder|SalesItem whereTotal($value)
 * @method static Builder|SalesItem whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property-read Collection|TaxRate[] $taxes
 * @property-read int|null $taxes_count
 */

class PurchasedItem extends Model
{
    /**
     * @var string
     */
    protected $table = 'purchased_items';

    /**
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'item',
        'category_id',
        'service_id',
        'description',
        'quantity',
        'tax',
        'rate',
        'total',
        'discount',
        'taxable',
        'unit_id'

    ];

    protected $casts = [
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'item' => 'string',
        'description' => 'string',
        'quantity' => 'double',
        'rate' => 'double',
        'total' => 'double',
    ];

    /**
     * @return BelongsToMany
     */
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxRate::class,
            'sales_item_taxes',
            'sales_item_id',
            'tax_id'
        )->withTimestamps();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class, 'service_id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'owner_id', 'id');
    }
    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class, 'owner_id', 'id');
    }
}
