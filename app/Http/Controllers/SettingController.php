<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Repositories\SettingRepository;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Money\Currency;

class SettingController extends AppBaseController
{
    /** @var SettingRepository */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }


    /**
     * Display the specified Setting.
     *
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function show(Request $request)
    {
        $groupName = $request->get('group', 'company_information');
        $settings = $this->settingRepository->getSyncList($groupName);
        $currencies = $this->settingRepository->getCurrencies();
        $dateFormats = $this->settingRepository->getDateFormats();
        $countries = $this->settingRepository->getCountries();
        $states = $this->settingRepository->getStates();
        return view('settings.show', compact('settings', 'groupName', 'currencies', 'dateFormats', 'countries', 'states'));
    }

    /**
     * Update the specified Setting in storage.
     *
     * @param  UpdateSettingRequest  $request
     * @return RedirectResponse
     */
    public function update(UpdateSettingRequest $request)
    {



        if ($request->get('group') == Setting::COMPANY_INFORMATION) {
            $request['phone'] = preparePhoneNumber($request, 'phone');
        }

        if (!isset($request['is_round'])) {
            $request['is_round'] = 0;
        }


        $this->settingRepository->updateSetting($request->all());

        Flash::success(__('messages.setting.setting_updated_successfully'));

        return redirect()->back();
    }
}
