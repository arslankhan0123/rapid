<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * Class Customer
 *
 * @version April 3, 2020, 6:37 am UTC
 *
 * @property int $id
 * @property string $company_name
 * @property string|null $vat_number
 * @property string|null $phone
 * @property string|null $website
 * @property string|null $currency
 * @property string|null $country
 * @property string|null $default_language
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereCompanyName($value)
 * @method static Builder|Customer whereCountry($value)
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereCurrency($value)
 * @method static Builder|Customer whereDefaultLanguage($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer wherePhone($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @method static Builder|Customer whereVatNumber($value)
 * @method static Builder|Customer whereWebsite($value)
 * @mixin Eloquent
 *
 * @property-read Address $address
 * @property-read Collection|CustomerGroup[] $customerGroups
 * @property-read int|null $customer_groups_count
 */
class Customer extends Model
{
    const LANGUAGES = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'tr' => 'Turkish',
    ];

    const CURRENCIES = [
        '0' => 'SAR',
        '1' => 'AED',
        '2' => 'USD',
        // '3' => 'AUD',
        // '4' => 'EUR',
        // '5' => 'JPY',
        // '6' => 'GBP',
        // '7' => 'CAD',
        // '8' => 'CHF', // Swiss Franc
        // '9' => 'CNY', // Chinese Yuan
        // '10' => 'INR', // Indian Rupee
        // '11' => 'MXN', // Mexican Peso
        // '12' => 'RUB', // Russian Ruble
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    // public static $rules = [
    //     'company_name' => 'required|unique:customers,company_name',
    //     'code' => 'required|unique:customers,code|alpha_num',
    //     'phone' => 'nullable|unique:customers,phone',
    //     'zip' => 'nullable|max:6',
    //     'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
    //     'country' => "required",
    //     'state' => "required",
    //     'currency' => "required",
    //     'default_language' => "required",
    //     'inactive' => "nullable",
    //     'location_url' => "nullable",
    //     'customer_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Allow only JPG, JPEG, PNG images
    //     'fax' => "nullable",
    //     'address' => "nullable",
    //     'short_name' => "nullable",
    //     'vendor_code' => 'nullable',

    // ];

    public static $rules = [
        'company_name' => 'required|unique:customers,company_name',
        'code' => 'required|unique:customers,code|alpha_num',

        // Phone numbers validation
        'phone' => 'nullable|unique:customers,phone',
        'fax'      => 'nullable|regex:/^[0-9]{7,15}$/',
        'mobile'   => 'nullable|regex:/^[0-9]{7,15}$/',
        'whatsapp' => 'nullable|regex:/^[0-9]{7,15}$/',
        'email' => 'nullable|email:rfc,dns|unique:customers,email|max:191',

        'zip' => 'nullable|max:6',
        'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'country' => "required",
        'state' => "required",
        'currency' => "required",
        'default_language' => "required",
        'inactive' => "nullable",
        'location_url' => "nullable",
        'customer_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'address' => "nullable",
        'short_name' => "nullable",
        'vendor_code' => 'nullable',
        'opening_balance' => 'nullable|numeric|min:0',

    ];



    /**
     * @var string[]
     */
    public static $editRules = [
        'code' => 'required|unique:customers,code|alpha_num',
        'zip' => 'nullable|max:6',
        'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'vendor_code' => 'nullable',
    ];

    /**
     * @var string
     */
    protected $table = 'customers';

    /**
     * @var string[]
     */
    protected $fillable = [
        'company_name',
        'code',
        'vat_number',
        'phone',
        'website',
        'currency',
        'country',
        'default_language',
        'mobile',
        'fax',
        'customer_logo',
        'address',
        'location_url',
        'inactive',
        'short_name',
        'whatsapp',
        'email',
        'vendor_code',
        'opening_balance'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'company_name' => 'string',
        'vat_number' => 'string',
        'website' => 'string',
        'phone' => 'string',
        'currency' => 'string',
        'country' => 'string',
        'default_language' => 'string',
    ];

    public static function messages()
    {
        return [
            'company_name.required' => 'The company name is required.',
            'company_name.unique' => 'This company name is already registered.',
            'code.required' => 'The customer code is required.',
            'code.unique' => 'This customer code is already taken.',
            'code.alpha_num' => 'Customer code must contain only letters and numbers.',
            'phone.unique' => 'This phone number is already registered.',
            'zip.max' => 'ZIP code cannot exceed 6 characters.',
            'website.regex' => 'Please provide a valid website URL.',
            'country.required' => 'The country field is required.',
            'state.required' => 'The state field is required.',
            'currency.required' => 'The currency field is required.',
            'default_language.required' => 'The default language field is required.',
            'customer_logo.image' => 'The customer logo must be an image.',
            'customer_logo.mimes' => 'The customer logo must be a JPG, JPEG, or PNG file.',
            'customer_logo.max' => 'The customer logo cannot exceed 2MB in size.',
            'phone.regex'    => 'Phone number must be digits only and between 7 to 15 characters.',
            'mobile.regex'   => 'Mobile number must be digits only and between 7 to 15 characters.',
            'whatsapp.regex' => 'WhatsApp number must be digits only and between 7 to 15 characters.',
            'fax.regex'      => 'Fax number must be digits only and between 7 to 15 characters.',
            'email.email'    => 'Please enter a valid email address.',
            'email.unique'   => 'This email is already registered.',
            'email.max'      => 'Email cannot exceed 191 characters.',
            'opening_balance.min' => 'Opening balance must be at least 0.',

        ];
    }

    /**
     * @return BelongsToMany
     */
    public function customerGroups(): BelongsToMany
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_to_customer_groups');
    }

    /**
     * @return MorphOne
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'owner');
    }
    public function customerAddress(): hasOne
    {
        return $this->hasOne(Address::class, 'owner_id');
    }

    /**
     * @return Model|MorphOne|object|null
     */
    public function billingAddress()
    {
        return $this->morphOne(Address::class, 'owner')
            ->where('type', '=', Address::ADDRESS_TYPES[1])->first();
    }

    /**
     * @return Model|MorphOne|object|null
     */
    public function shippingAddress()
    {
        return $this->morphOne(Address::class, 'owner')
            ->where('type', '=', Address::ADDRESS_TYPES[2])->first();
    }

    /**
     * @return HasOne
     */
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class, 'customer_id');
    }

    /**
     * @return HasOne
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'customer_id');
    }

    /**
     * @return HasOne
     */
    public function creditNote(): HasOne
    {
        return $this->hasOne(CreditNote::class, 'customer_id');
    }

    /**
     * @return HasOne
     */
    public function estimate(): HasOne
    {
        return $this->hasOne(Estimate::class, 'customer_id');
    }

    /**
     * @return HasOne
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'customer_id');
    }

    /**
     * @return HasOne
     */
    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'customer_id');
    }

    /**
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'owner_id');
    }

    /**
     * @return HasOne
     */
    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class, 'owner_id');
    }

    /**
     * @return BelongsTo
     */
    public function customerCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }
    public function customerPayment(): HasMany
    {
        return $this->hasMany(CustomerPayment::class, 'customer_id');
    }

    public function documents()
    {
        return $this->hasMany(CustomerDoc::class);
    }
}
