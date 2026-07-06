<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class PaymentMode
 *
 * @version April 6, 2020, 9:55 am UTC
 *
 * @property string name
 * @property string description
 * @property bool active
 * @property bool show_on_pdf
 * @property bool selected_by_default
 * @property bool invoices_only
 * @property bool expenses_only
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|PaymentMode newModelQuery()
 * @method static Builder|PaymentMode newQuery()
 * @method static Builder|PaymentMode query()
 * @method static Builder|PaymentMode whereActive($value)
 * @method static Builder|PaymentMode whereCreatedAt($value)
 * @method static Builder|PaymentMode whereDescription($value)
 * @method static Builder|PaymentMode whereId($value)
 * @method static Builder|PaymentMode whereName($value)
 * @method static Builder|PaymentMode whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PaymentMode extends Model
{
    const ACTIVE = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    /**
     * @var string
     */
    protected $table = 'payment_modes';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'active',
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
        'active' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => [
            'required',
            'unique:payment_modes,name',
            'regex:/^[\p{L}\p{M}\s]+$/u'
        ],
    ];

    /**
     * @return BelongsToMany
     */
    public function paymentModesForInvoice(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'invoice_payment_modes');
    }
}
