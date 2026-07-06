<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property int $group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $media_count
 *
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereGroup($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereKey($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 * @mixin Eloquent
 */
class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const PATH = 'settings';

    public const FAVICON = 'settings/favicon';

    const GROUP_GENERAL = 1;

    const COMPANY_INFORMATION = 2;

    const NOTE = 3;

    const GROUP_ARRAY = [
        'general' => self::GROUP_GENERAL,
        'company_information' => self::COMPANY_INFORMATION,
        'note' => self::NOTE,
    ];

    const CURRENCIES = [
        'eur' => 'Euro (EUR)',
        'aud' => 'Australia Dollar (AUD)',
        'inr' => 'India Rupee (INR)',
        'usd' => 'USA Dollar (USD)',
        'jpy' => 'Japanese Yen (JPY)',
        'gbp' => 'British Pound (GBP)',
        'cad' => 'Canadian Dollar (CAD)',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'group' => 'required|integer',
    ];

    /**
     * @var string
     */
    protected $table = 'settings';

    /**
     * @var string[]
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'key' => 'string',
        'value' => 'string',
    ];

    public static function getLogoLocalPath(): ?string
    {
        $logo = self::where('key', 'logo')->value('value');

        if (!$logo) {
            return null;
        }

        $path = parse_url($logo, PHP_URL_PATH); // e.g., /uploads/1641/magento-logo.png

        $segments = explode('/', trim($path, '/'));

        if (count($segments) < 2) {
            return null;
        }

        $folder = $segments[count($segments) - 2];
        $file = $segments[count($segments) - 1];

        return public_path("uploads/{$folder}/{$file}");
    }



}
