<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class SalesTax
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string $tax
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|SalesTax newModelQuery()
 * @method static Builder|SalesTax newQuery()
 * @method static Builder|SalesTax query()
 * @method static Builder|SalesTax whereAmount($value)
 * @method static Builder|SalesTax whereCreatedAt($value)
 * @method static Builder|SalesTax whereId($value)
 * @method static Builder|SalesTax whereOwnerId($value)
 * @method static Builder|SalesTax whereOwnerType($value)
 * @method static Builder|SalesTax whereTax($value)
 * @method static Builder|SalesTax whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SalesTax extends Model
{
    /**
     * @var string
     */
    protected $table = 'sales_taxes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'tax',
        'amount',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'tax' => 'string',
        'amount' => 'double',
    ];
}
