<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class ContractType
 *
 * @version April 8, 2020, 4:20 am UTC
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ContractType newModelQuery()
 * @method static Builder|ContractType newQuery()
 * @method static Builder|ContractType query()
 * @method static Builder|ContractType whereCreatedAt($value)
 * @method static Builder|ContractType whereDescription($value)
 * @method static Builder|ContractType whereId($value)
 * @method static Builder|ContractType whereName($value)
 * @method static Builder|ContractType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ContractType extends Model
{
    /**
     * @var string
     */
    protected $table = 'contract_types';

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
        'name' => 'required|unique:contract_types,name',
        'description' => 'nullable',
    ];

    /**
     * @return HasMany
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'contract_type_id');
    }

    /**
     * @return HasMany
     */
    public function contractValues(): HasMany
    {
        return $this->hasMany(Contract::class, 'contract_type_id')->whereNotNull('contract_value');
    }

    /**
     * @return HasMany
     */
    public function contractsCustomer(): HasMany
    {
        $customerId = Auth::user()->contact->customer->id;

        return $this->hasMany(Contract::class, 'contract_type_id')->where('customer_id', $customerId);
    }
}
