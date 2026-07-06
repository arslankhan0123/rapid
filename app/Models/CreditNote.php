<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\CreditNote
 *
 * @property int $id
 * @property int $customer_id
 * @property string $credit_note_number
 * @property Carbon $credit_note_date
 * @property int $currency
 * @property int|null $discount_type
 * @property float|null $discount
 * @property string|null $admin_text
 * @property int $unit
 * @property string|null $client_note
 * @property string|null $term_conditions
 * @property float|null $sub_total
 * @property float $adjustment
 * @property float|null $total_amount
 * @property int|null $payment_status
 * @property int|null $discount_symbol
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|CreditNoteAddress[] $creditNoteAddresses
 * @property-read int|null $credit_note_addresses_count
 * @property-read Customer $customer
 * @property-read Collection|SalesItem[] $salesItems
 * @property-read int|null $sales_items_count
 * @property-read Collection|SalesTax[] $salesTaxes
 * @property-read int|null $sales_taxes_count
 *
 * @method static Builder|CreditNote newModelQuery()
 * @method static Builder|CreditNote newQuery()
 * @method static Builder|CreditNote query()
 * @method static Builder|CreditNote whereAdjustment($value)
 * @method static Builder|CreditNote whereAdminText($value)
 * @method static Builder|CreditNote whereClientNote($value)
 * @method static Builder|CreditNote whereCreatedAt($value)
 * @method static Builder|CreditNote whereCreditNoteDate($value)
 * @method static Builder|CreditNote whereCreditNoteNumber($value)
 * @method static Builder|CreditNote whereCurrency($value)
 * @method static Builder|CreditNote whereCustomerId($value)
 * @method static Builder|CreditNote whereDiscount($value)
 * @method static Builder|CreditNote whereDiscountType($value)
 * @method static Builder|CreditNote whereId($value)
 * @method static Builder|CreditNote wherePaymentStatus($value)
 * @method static Builder|CreditNote whereSubTotal($value)
 * @method static Builder|CreditNote whereTermConditions($value)
 * @method static Builder|CreditNote whereTotalAmount($value)
 * @method static Builder|CreditNote whereUnit($value)
 * @method static Builder|CreditNote whereUpdatedAt($value)
 * @mixin Model
 *
 * @property string|null $reference
 *
 * @method static Builder|CreditNote whereReference($value)
 */
class CreditNote extends Model implements \App\Models\Contracts\Taggable
{
    const PAYMENT_STATUS = [
        3 => 'Closed',
        0 => 'Drafted',
        1 => 'Open',
        2 => 'Void',
    ];

    const STATUS_COLOR = [
        0 => 'warning',
        1 => 'danger',
        2 => 'primary',
        3 => 'success',
    ];

    const PAYMENT_STATUS_DRAFT = 0;

    const PAYMENT_STATUS_OPEN = 1;

    const PAYMENT_STATUS_VOID = 2;

    const PAYMENT_STATUS_CLOSED = 3;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required',
        'customer_id' => 'required',
        'credit_note_number' => 'required',
        'credit_note_date' => 'required',
        'currency' => 'required',
        'discount_type' => 'nullable',
        'unit' => 'nullable',
        // 'itemsArr' => 'required|array',
        // 'itemsArr.*.item' => 'required',
        // 'itemsArr.*.quantity' => 'required',
        // 'itemsArr.*.rate' => 'required',
        // 'invoice_id' => 'required'
    ];

    /**
     * @var array
     */
    public static $messages = [
        'customer_id.required' => 'Customer field is required.',
        // 'itemsArr.required' => 'Atleast one product is required.',
        // 'itemsArr.*.item.required' => 'Product field is required.',
        // 'itemsArr.*.quantity.required' => 'Quantity field is required',
        // 'itemsArr.*.rate.required' => 'Rate field is required',

    ];

    /**
     * @var string
     */
    protected $table = 'credit_notes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'title',
        'credit_note_number',
        'credit_note_date',
        'currency',
        'discount_type',
        'reference',
        'admin_text',
        'unit',
        'client_note',
        'term_conditions',
        'total_amount',
        'sub_total',
        'discount',
        'adjustment',
        'payment_status',
        'discount_symbol',
        'invoice_id',
        'percentage_discount',
        'vendor_code',
        'user_id',
        'branch_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'customer_id' => 'integer',
        'credit_note_number' => 'string',
        'credit_note_date' => 'datetime',
        'currency' => 'integer',
        'discount_type' => 'integer',
        'reference' => 'string',
        'admin_text' => 'string',
        'total_amount' => 'double',
        'sub_total' => 'double',
        'discount' => 'double',
        'adjustment' => 'double',
        'discount_symbol' => 'integer',
        'payment_status' => 'integer',
        'unit' => 'integer',
        'client_note' => 'string',
    ];

    /**
     * @return string
     */
    public static function generateUniqueCreditNoteId(): string
    {
        $creditNoteId = mb_strtoupper(Str::random(6));
        while (true) {
            $isExist = self::whereCreditNoteNumber($creditNoteId)->exists();
            if ($isExist) {
                self::generateUniqueCreditNoteId();
            }
            break;
        }

        return $creditNoteId;
    }

    /**
     * @param  int  $currencyId
     * @return string
     */
    public static function getCurrencyText($currencyId): string
    {
        return Customer::CURRENCIES[$currencyId];
    }

    /**
     * @return HasMany
     */
    public function creditNoteAddresses(): HasMany
    {
        return $this->hasMany(CreditNoteAddress::class);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOwnerType(): string
    {
        return self::class;
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    // In YourModel.php
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_number');
    }
    /**
     * @return MorphMany
     */
    public function salesItems(): MorphMany
    {
        return $this->morphMany(SalesItem::class, 'owner');
    }

    /**
     * @return MorphMany
     */
    public function salesTaxes(): MorphMany
    {
        return $this->morphMany(SalesTax::class, 'owner');
    }

    /**
     * @param  int  $discountTypeId
     * @return string
     */
    public function getDiscountTypeText($discountTypeId): string
    {
        return $this->discountType()[$discountTypeId];
    }

    /**
     * @return array
     */
    public static function discountType(): array
    {
        return [
            '0' => 'No Discount',
            '1' => 'Before Tax',
            '2' => 'After Tax',
        ];
    }

    public function terms()
    {
        return $this->hasMany(CreditNoteTerms::class, 'credit_note_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
