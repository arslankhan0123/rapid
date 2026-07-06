<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Country;
use App\Models\SupplierToGroup;

class Supplier extends Model
{
    use HasFactory;

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
        '2' => 'AUD',
        '3' => 'USD',
        '4' => 'EUR',
        '5' => 'JPY',
        '6' => 'GBP',
        '7' => 'CAD',
    ];

    public static $rules = [
        'company_name' => 'required|unique:suppliers,company_name',
    ];

    /**
     * @var string
     */
    protected $table = 'suppliers';

    /**
     * @var array
     */
    protected $fillable = [
        'company_name',
        'vat_number',
        'phone',
        'website',
        'currency',
        'country',
        'default_language',
        'street',
        'city',
        'state',
        'zip',
        'contact_person',
        'email',
        'mailing_address',
        'po_box',
        'whatsapp',
        'opening_balance'
    ];



    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'company_name' => 'string',
        'vat_number' => 'string',
        'phone' => 'string',
        'website' => 'string',
        'currency' => 'integer',
        'country' => 'integer',
        'default_language' => 'string',
        'street' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip' => 'string',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }
    public function supplierCountry()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }
    public function supplierState()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function groups()
    {
        return $this->hasMany(SupplierToGroup::class, 'supplier_id');
    }


    public function supplierGroups()
    {
        return $this->hasMany(SupplierToGroup::class, 'supplier_id');
    }
    public function documents()
    {
        return $this->hasMany(SupplierDoc::class);
    }
}
