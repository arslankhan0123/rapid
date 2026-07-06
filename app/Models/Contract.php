<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Contract
 *
 * @property int $id
 * @property string $subject
 * @property string|null $description
 * @property string|null $start_date
 * @property Carbon|null $end_date
 * @property float|null $contract_value
 * @property int $customer_id
 * @property int $contract_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Customer $customer
 * @property-read ContractType $type
 *
 * @method static Builder|Contract newModelQuery()
 * @method static Builder|Contract newQuery()
 * @method static Builder|Contract query()
 * @method static Builder|Contract whereContractTypeId($value)
 * @method static Builder|Contract whereContractValue($value)
 * @method static Builder|Contract whereCreatedAt($value)
 * @method static Builder|Contract whereCustomerId($value)
 * @method static Builder|Contract whereDescription($value)
 * @method static Builder|Contract whereEndDate($value)
 * @method static Builder|Contract whereId($value)
 * @method static Builder|Contract whereStartDate($value)
 * @method static Builder|Contract whereSubject($value)
 * @method static Builder|Contract whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Contract extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|unique:contracts,subject',
        'customer_id' => 'required',
        'contract_type_id' => 'required',
    ];

    /**
     * @var string
     */
    protected $table = 'contracts';

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'contract_type_id',
        'start_date',
        'end_date',
        'subject',
        'contract_value',
        'description',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'contract_type_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'subject' => 'string',
        'description' => 'string',
        'contract_value' => 'double',
    ];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }
}
