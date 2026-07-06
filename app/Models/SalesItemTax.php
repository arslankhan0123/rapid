<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\SalesTaxes
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property int $tax_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|SalesItemTax newModelQuery()
 * @method static Builder|SalesItemTax newQuery()
 * @method static Builder|SalesItemTax query()
 * @method static Builder|SalesItemTax whereCreatedAt($value)
 * @method static Builder|SalesItemTax whereId($value)
 * @method static Builder|SalesItemTax whereOwnerId($value)
 * @method static Builder|SalesItemTax whereOwnerType($value)
 * @method static Builder|SalesItemTax whereTaxId($value)
 * @method static Builder|SalesItemTax whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property int $sales_item_id
 * @property-read TaxRate $salesTaxes
 *
 * @method static Builder|SalesItemTax whereSalesItemId($value)
 */
class SalesItemTax extends Model
{
    /**
     * @var string
     */
    protected $table = 'sales_item_taxes';

    /**
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'tax_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'tax_id' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function salesTaxes(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_id');
    }
}
