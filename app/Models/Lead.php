<?php

namespace App\Models;

use App\Models\Contracts\Taggable;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use App\Models\Item;
use App\Models\ItemGroup;

/**
 * Class Lead
 *
 * @version April 20, 2020, 12:43 pm UTC
 *
 * @property int $id
 * @property int $status_id
 * @property int $source_id
 * @property int|null $assign_to
 * @property string $name
 * @property string|null $position
 * @property string|null $email
 * @property int|null $estimate_budget
 * @property string|null $website
 * @property string|null $phone
 * @property string|null $company
 * @property string|null $description
 * @property int|null $default_language
 * @property int|null $public
 * @property int|null $contacted_today
 * @property string|null $date_contacted
 * @property string|null $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Address $address
 * @property-read LeadSource $leadSource
 * @property-read LeadStatus $leadStatus
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 *
 * @method static Builder|Lead newModelQuery()
 * @method static Builder|Lead newQuery()
 * @method static Builder|Lead query()
 * @method static Builder|Lead whereAssignTo($value)
 * @method static Builder|Lead whereCompany($value)
 * @method static Builder|Lead whereContactedToday($value)
 * @method static Builder|Lead whereCreatedAt($value)
 * @method static Builder|Lead whereDateContacted($value)
 * @method static Builder|Lead whereDefaultLanguage($value)
 * @method static Builder|Lead whereDescription($value)
 * @method static Builder|Lead whereEmail($value)
 * @method static Builder|Lead whereId($value)
 * @method static Builder|Lead whereName($value)
 * @method static Builder|Lead wherePhone($value)
 * @method static Builder|Lead wherePosition($value)
 * @method static Builder|Lead wherePublic($value)
 * @method static Builder|Lead whereSourceId($value)
 * @method static Builder|Lead whereStatusId($value)
 * @method static Builder|Lead whereUpdatedAt($value)
 * @method static Builder|Lead whereWebsite($value)
 *  * @method static Builder|Lead whereEstimateBudget($value)
 * @mixin Eloquent
 *
 * @property-read User|null $assignedTo
 * @property string $company_name
 * @property-read Collection|Note[] $notes
 * @property-read int|null $notes_count
 *
 * @method static Builder|Lead whereCompanyName($value)
 *
 * @property int $lead_convert_customer
 * @property string|null $lead_convert_date
 *
 * @method static Builder|Lead whereLeadConvertCustomer($value)
 * @method static Builder|Lead whereLeadConvertDate($value)
 * @method static Builder|Lead whereCountry($value)
 */
class Lead extends Model
{
    protected $table = 'leads';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'product_group_id',
        'product_id',
        'budget',
        'priority',
        'start_date',
        'assignee',
        'contact',
        'position',
        'source_id',
        'employees',
        'branches',
        'business',
        'automation',
        'status_id',
        'default_language',
        'mobile',
        'whatsapp',
        'phone',
        'fax',
        'email',
        'website',
        'country_id',
        'state_id',
        'city_id',
        'area_id',
        'facebook',
        'instagram',
        'linkedin',
        'location',
        'description',
        'inserted_by'
    ];

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

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client_name' => 'string',
        'product_group_id' => 'integer',
        'product_id' => 'integer',
        'budget' => 'double',
        'priority' => 'string',
        'start_date' => 'date',
        'assignee' => 'integer',
        'contact' => 'string',
        'position' => 'string',
        'source_id' => 'integer',
        'employees' => 'integer',
        'branches' => 'integer',
        'business' => 'integer',
        'automation' => 'boolean',
        'status_id' => 'integer',
        'default_language' => 'string',
        'mobile' => 'string',
        'whatsapp' => 'string',
        'phone' => 'string',
        'fax' => 'string',
        'email' => 'string',
        'website' => 'string',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city_id' => 'integer',
        'area_id' => 'integer',
        'facebook' => 'string',
        'instagram' => 'string',
        'linkedin' => 'string',
        'location' => 'string',
        'description' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'product_group_id' => 'required',
        'product_id' => 'required',
        'contact' => 'required',
        'mobile' => 'required',
        'status_id' => 'required',
        'source_id' => 'required',
        'business' => 'required',
        'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'email' => 'nullable|email',
        'inserted_by'=> 'required'
    ];

    /**
     * @var string[]
     */
    public static $editRules = [
        'status_id' => 'required',
        'source_id' => 'required',
        'business' => 'required',
        'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'email' => 'nullable|email',
    ];

    /**
     * @return BelongsTo
     */
    public function leadStatus(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }

    /**
     * @return BelongsTo
     */
    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    /**
     * @return BelongsTo
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee');
    }
    /**
     * @return BelongsTo
     */
    public function leadCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    /**
     * @return BelongsTo
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function productGroup(){
        return $this->belongsTo(ItemGroup::class,'product_group_id');
    }
    public function product()
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    // Method to get the language name
    public function getLanguageNameAttribute()
    {
        return self::LANGUAGES[$this->default_language] ?? 'Unknown'; // replace 'language_code' with the actual column name
    }

}
