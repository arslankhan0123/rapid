<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class TaxRate
 *
 * @version April 6, 2020, 6:48 am UTC
 *
 * @property int $id
 * @property string $name
 * @property float $tax_rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|TaxRate newModelQuery()
 * @method static Builder|TaxRate newQuery()
 * @method static Builder|TaxRate query()
 * @method static Builder|TaxRate whereCreatedAt($value)
 * @method static Builder|TaxRate whereId($value)
 * @method static Builder|TaxRate whereName($value)
 * @method static Builder|TaxRate whereTaxRate($value)
 * @method static Builder|TaxRate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TaxRate extends Model
{
    /**
     * @var string
     */
    protected $table = 'tax_rates';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'tax_rate',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'tax_rate' => 'double',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:tax_rates,name',
        'tax_rate' => 'required|numeric|min:0|max:100',
    ];
}
