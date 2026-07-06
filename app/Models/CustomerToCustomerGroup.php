<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\CustomerToCustomerGroup
 *
 * @property int $id
 * @property int $customer_id
 * @property int $customer_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|CustomerToCustomerGroup newModelQuery()
 * @method static Builder|CustomerToCustomerGroup newQuery()
 * @method static Builder|CustomerToCustomerGroup query()
 * @method static Builder|CustomerToCustomerGroup whereCreatedAt($value)
 * @method static Builder|CustomerToCustomerGroup whereCustomerGroupId($value)
 * @method static Builder|CustomerToCustomerGroup whereCustomerId($value)
 * @method static Builder|CustomerToCustomerGroup whereId($value)
 * @method static Builder|CustomerToCustomerGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CustomerToCustomerGroup extends Model
{
    /**
     * @var string
     */
    public $table = 'customer_to_customer_groups';

    /**
     * @var array
     */
    public $fillable = [
        'customer_id',
        'customer_group_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];
}
