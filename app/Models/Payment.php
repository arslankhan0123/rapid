<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string $amount_received
 * @property string $payment_date
 * @property int $payment_mode
 * @property string|null $transaction_id
 * @property string|null $note
 * @property int|null $send_mail_to_customer_contacts
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment query()
 * @method static Builder|Payment whereAmountReceived($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereNote($value)
 * @method static Builder|Payment whereOwnerId($value)
 * @method static Builder|Payment whereOwnerType($value)
 * @method static Builder|Payment wherePaymentDate($value)
 * @method static Builder|Payment wherePaymentMode($value)
 * @method static Builder|Payment whereSendMailToCustomerContacts($value)
 * @method static Builder|Payment whereTransactionId($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property-read InvoicePaymentMode $invoicePaymentMode
 */
class Payment extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'amount_received' => 'required',
        'payment_date' => 'required',
        'payment_mode' => 'required',
        'transaction_id' => 'nullable|unique:payments,transaction_id',
    ];

    /**
     * @var string
     */
    protected $table = 'payments';

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'amount_received',
        'payment_date',
        'payment_mode',
        'transaction_id',
        'note',
        'send_mail_to_customer_contacts',
        'stripe_id',
        'meta',
        'branch_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'payment_mode' => 'integer',
        'transaction_id' => 'string',
        'amount_received' => 'double',
        'payment_date' => 'datetime',
        'note' => 'string',
        'send_mail_to_customer_contacts' => 'boolean',
        'stripe_id' => 'string',
        'meta' => 'string',
    ];

    /**
     * @return BelongsTo
     */
    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'owner_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
