<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Expense
 *
 * @version April 20, 2020, 5:16 am UTC
 *
 * @property string name
 * @property string|null $note
 * @property int expense_category_id
 * @property Carbon expense_date
 * @property float amount
 * @property int|null $customer_id
 * @property int currency
 * @property int|null $tax_1_id
 * @property int|null $tax_2_id
 * @property int|null $payment_mode_id
 * @property string|null $reference
 *
 * @method static Builder|Expense newModelQuery()
 * @method static Builder|Expense newQuery()
 * @method static Builder|Expense query()
 * @mixin Eloquent
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Expense whereAmount($value)
 * @method static Builder|Expense whereCreatedAt($value)
 * @method static Builder|Expense whereCurrency($value)
 * @method static Builder|Expense whereCustomerId($value)
 * @method static Builder|Expense whereExpenseCategoryId($value)
 * @method static Builder|Expense whereExpenseDate($value)
 * @method static Builder|Expense whereId($value)
 * @method static Builder|Expense whereName($value)
 * @method static Builder|Expense whereNote($value)
 * @method static Builder|Expense wherePaymentModeId($value)
 * @method static Builder|Expense whereReference($value)
 * @method static Builder|Expense whereTax1($value)
 * @method static Builder|Expense whereTax2($value)
 * @method static Builder|Expense whereUpdatedAt($value)
 *
 * @property-read Customer|null $customer
 * @property-read ExpenseCategory $expenseCategory
 * @property-read Collection|PaymentMode[] $paymentMode
 * @property-read int|null $payment_mode_count
 * @property-read TaxRate|null $taxRate1
 * @property-read TaxRate|null $taxRate2
 * @property-read int|null $media_count
 * @property-read TaxRate|null $tax1Rate
 * @property-read TaxRate|null $tax2Rate
 *
 * @method static Builder|Expense whereBillable($value)
 * @method static Builder|Expense whereTax1Id($value)
 * @method static Builder|Expense whereTax2Id($value)
 * @method static Builder|Expense whereTaxApplied($value)
 *
 * @property bool|null $tax_applied
 * @property int|null $tax_rate
 * @property int|null $billable
 *
 * @method static Builder|Expense whereTaxRate($value)
 *
 * @property-read bool|string $expense_attachment
 */
class Expense extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const EXPENSE_RECEIPT = 'expense';

    const CURRENCIES = [
        '0' => 'Indian Rupee',
        '1' => 'Spanish Dollar',
        '2' => 'USA Dollar',
        '3' => 'Canada Dollar',
        '4' => 'Germany Dollar',
        '5' => 'China Dollar',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'expense_category_id' => 'required',
        'amount' => 'required|numeric|min:0.01',
        'currency' => 'required',
        'receipt_attachment' => 'nullable|mimes:jpeg,png,pdf,docx,doc',
        'expense_number' => 'required'
    ];

    /**
     * @var string
     */
    protected $table = 'expenses';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'note',
        'expense_category_id',
        'expense_date',
        'amount',
        'customer_id',
        'currency',
        'tax_applied',
        'tax_1_id',
        'tax_2_id',
        'tax_rate',
        'payment_mode_id',
        'reference',
        'billable',
        'expense_number',
        'user_id',
        'branch_id',
        'pur_inv_number',
        'supp_vat_number',
        'employee_id',
        'employee_name',
        'expense_for',
        'sub_category_id',
        'isTaxable',
        'supplier_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'note' => 'string',
        'expense_category_id' => 'integer',
        'expense_date' => 'datetime',
        'amount' => 'double',
        'customer_id' => 'integer',
        'currency' => 'integer',
        'tax_applied' => 'boolean',
        'tax_1_id' => 'integer',
        'tax_2_id' => 'integer',
        'tax_rate' => 'double',
        'payment_mode_id' => 'integer',
        'reference' => 'string',
        'billable' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $appends = ['expense_attachment'];

    /**
     * @return bool|string
     */
    public function getExpenseAttachmentAttribute()
    {
        $media = $this->getMedia(self::EXPENSE_RECEIPT)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return false;
    }

    /**
     * @return BelongsTo
     */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    public function expenseSubCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseSubCategory::class, 'sub_category_id');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'customer_id');
    }
    public function currencyNew(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency');
    }

    /**
     * @return belongsTo
     */
    // public function paymentMode(): BelongsTo
    // {
    //     return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    // }
    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'payment_mode_id');
    }

    /**
     * @return belongsTo
     */
    public function tax1Rate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_1_id');
    }

    /**
     * @return belongsTo
     */
    public function tax2Rate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_2_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'owner_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function documents()
    {
        return $this->hasMany(ExpenseDoc::class);
    }
}
