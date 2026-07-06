<?php

namespace App\Repositories;

use App\Models\Setting;
use Arr;
use App\Models\Currency;
use App\Models\Country;
use App\Models\State;

/**
 * Class SettingRepository
 *
 * @version April 23, 2020, 1:45 pm UTC
 */
class SettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_main_domain',
        'company_name',
        'note',
        'overtime_rate'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
    public function getDateFormats()
    {
        return  $dateFormats= [
            'd-m-Y' => 'DD-MMM-YYYY',
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y-m-d' => 'YYYY-MM-DD',
            'd M, Y' => 'DD MMM, YYYY',
            'M d, Y' => 'MMM DD, YYYY',
            'Y M d' => 'YYYY MMM DD',
        ];
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Setting::class;
    }

    /**
     * @param  string  $groupName
     * @return array
     */
    public function getSyncList($groupName)
    {
        return Setting::pluck('value', 'key')->toArray();
    }
    public function getCurrencies()
    {
        return Currency::pluck('name', 'id');
    }
    public function getCountries()
    {
        return Country::pluck('name', 'id');
    }
    public function getStates()
    {
        return State::get();
    }


    /**
     * @param  array  $input
     * @return bool
     */
    public function updateSetting($input)
    {
        $inputArr = Arr::except($input, ['_token']);
        $inputArr['vat_status']=isset($inputArr['vat_status'])?1:0;

        // dd($input);
        foreach ($inputArr as $key => $value) {

            /** @var Setting $setting */
            $setting = Setting::where('key', $key)->first();

            if (! $setting) {
                continue;
            }

            if (in_array($key, ['logo', 'favicon']) && ! empty($value)) {
                $this->fileUpload($setting, $value);
                continue;
            }

            $setting->update(['value' => $value]);
        }

        return true;
    }

    /**
     * @param  Setting  $setting
     * @param $file
     * @return Setting
     */
    public function fileUpload($setting, $file)
    {
        $setting->clearMediaCollection(Setting::PATH);
        $media = $setting->addMedia($file)->toMediaCollection(Setting::PATH, config('app.media_disc'));

        $setting->update(['value' => $media->getFullUrl()]);

        return $setting;
    }
}
