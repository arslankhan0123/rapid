<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\InvoiceTerm;

/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property int $customer_id
 * @property string $invoice_number
 * @property Carbon $invoice_date
 * @property Carbon|null $due_date
 * @property int|null $sales_agent_id
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
 * @property-read Customer $customer
 * @property-read Collection|InvoiceAddress[] $invoiceAddresses
 * @property-read int|null $invoice_addresses_count
 * @property-read Collection|PaymentMode[] $paymentModes
 * @property-read int|null $payment_modes_count
 * @property-read Collection|Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read Collection|SalesItem[] $salesItems
 * @property-read int|null $sales_items_count
 * @property-read Collection|SalesTax[] $salesTaxes
 * @property-read int|null $sales_taxes_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read User|null $user
 *
 * @method static Builder|Invoice newModelQuery()
 * @method static Builder|Invoice newQuery()
 * @method static Builder|Invoice query()
 * @method static Builder|Invoice whereAdjustment($value)
 * @method static Builder|Invoice whereAdminText($value)
 * @method static Builder|Invoice whereClientNote($value)
 * @method static Builder|Invoice whereCreatedAt($value)
 * @method static Builder|Invoice whereCurrency($value)
 * @method static Builder|Invoice whereCustomerId($value)
 * @method static Builder|Invoice whereDiscount($value)
 * @method static Builder|Invoice whereDiscountType($value)
 * @method static Builder|Invoice whereDueDate($value)
 * @method static Builder|Invoice whereId($value)
 * @method static Builder|Invoice whereInvoiceDate($value)
 * @method static Builder|Invoice whereInvoiceNumber($value)
 * @method static Builder|Invoice wherePaymentStatus($value)
 * @method static Builder|Invoice whereSalesAgentId($value)
 * @method static Builder|Invoice whereSubTotal($value)
 * @method static Builder|Invoice whereTermConditions($value)
 * @method static Builder|Invoice whereTotalAmount($value)
 * @method static Builder|Invoice whereUnit($value)
 * @method static Builder|Invoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Invoice extends Model implements \App\Models\Contracts\Taggable
{
    const STATUS_COLOR = [
        0 => 'warning',
        1 => 'primary',
        2 => 'success',
        3 => 'info',
        4 => 'danger',
    ];

    const PAYMENT_STATUS = [
        4 => 'Cancelled',
        0 => 'Drafted',
        2 => 'Paid',
        3 => 'Partially Paid',
        1 => 'Unpaid',
    ];

    const STATUS_DRAFT = 0;

    const STATUS_UNPAID = 1;

    const STATUS_PAID = 2;

    const STATUS_PARTIALLY_PAID = 3;

    const STATUS_CANCELLED = 4;

    const CLIENT_PAYMENT_STATUS = [
        4 => 'Cancelled',
        2 => 'Paid',
        3 => 'Partially Paid',
        1 => 'Unpaid',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'branch_id' => 'required',
        'title' => 'nullable',
        'customer_id' => 'required',
        'invoice_number' => 'required',
        'invoice_date' => 'required',
        'currency' => 'required',
        'unit' => 'nullable',
        'discount_type' => 'nullable',
        'payment_modes' => 'required',
        'total_amount' => 'nullable|numeric|min:0',   // total_amount should be a required numeric field, with a minimum value of 0
        'sub_total' => 'nullable|numeric|min:0',
        // 'itemsArr' => 'required|array',
        // 'itemsArr.*.item' => 'required',
        // 'itemsArr.*.quantity' => 'required',
        // 'itemsArr.*.rate' => 'required',
        // 'project_id'=>'required',
    ];

    /**
     * @var array
     */
    public static $messages = [
        'customer_id.required' => 'Customer field is required.',
        'payment_modes.required' => 'Payment Mode field is required.',
        // 'itemsArr.required' => 'Atleast one product is required.',
        // 'itemsArr.*.item.required' => 'Product field is required.',
        // 'itemsArr.*.quantity.required' => 'Quantity field is required',
        // 'itemsArr.*.rate.required' => 'Rate field is required',

    ];

    /**
     * @var string
     */
    protected $table = 'invoices';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'sales_agent_id',
        'currency',
        'discount_type',
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
        'hsn_tax',
        'project_id',
        'percentage_discount',
        'vendor_code',
        'user_id',
        'branch_id',
        'invoice_month'
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
        'invoice_number' => 'string',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sales_agent_id' => 'integer',
        'currency' => 'integer',
        'discount_type' => 'integer',
        'admin_text' => 'string',
        'total_amount' => 'double',
        'sub_total' => 'double',
        'discount' => 'double',
        'adjustment' => 'double',
        'payment_status' => 'integer',
        'discount_symbol' => 'integer',
        'client_note' => 'string',
        'term_conditions' => 'string',
        'unit' => 'integer',
        'hsn_tax' => 'string',
    ];

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

    /**
     * @return string
     */
    public static function generateUniqueInvoiceId(): string
    {
        $invoiceId = mb_strtoupper(Str::random(6));
        while (true) {
            $isExist = self::whereInvoiceNumber($invoiceId)->exists();
            if ($isExist) {
                self::generateUniqueInvoiceId();
            }
            break;
        }

        return $invoiceId;
    }

    /**
     * @return HasMany
     */
    public function invoiceAddresses(): HasMany
    {
        return $this->hasMany(InvoiceAddress::class);
    }

    /**
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return BelongsToMany
     */
    public function paymentModes(): BelongsToMany
    {
        return $this->belongsToMany(
            PaymentMode::class,
            'invoice_payment_modes',
            'invoice_id',
            'payment_mode_id'
        );
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_agent_id');
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
     * @param  int  $currencyId
     * @return string
     */
    public static function getCurrencyText($currencyId): string
    {
        return Customer::CURRENCIES[$currencyId];
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
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'owner');
    }

    /**
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'owner_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function terms()
    {
        return $this->hasMany(InvoiceTerm::class, 'invoice_id');
    }
    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class, 'invoice_id', 'invoice_number');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
