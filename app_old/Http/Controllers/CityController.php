<?php

namespace App\Http\Controllers;

use App\Queries\CityDataTable;
use Illuminate\Http\Request;
use App\Repositories\CityRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Http\Requests\UpdateCityRequest;
use Illuminate\Database\QueryException;
use Throwable;

class CityController extends AppBaseController
{
    /**
     * @var CityRepository
     */
    private $cityRepository;
    public function __construct(CityRepository $stateRepo)
    {
        $this->cityRepository = $stateRepo;
    }
    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new CityDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('cities.index');
    }

    public function create()
    {
        $countries=$this->cityRepository->getCountries();
        $states=$this->cityRepository->getStates();
        return view('cities.create',compact(['countries','states']));
    }

    public function store(CityRequest $request)
    {

        $input = $request->all();
        try {
            $city = $this->cityRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($city)
                ->useLog('City created.')
                ->log($city->name . ' City Created');
            return $this->sendResponse($city, __('messages.cities.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(City $city)
    {
        try {
            $city->delete();
            activity()->performedOn($city)->causedBy(getLoggedInUser())
                ->useLog('City deleted.')->log($city->name . ' City deleted.');
            return $this->sendSuccess(__('messages.cities.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(City $city)
    {

        $countries = $this->cityRepository->getCountries();
        $states = $this->cityRepository->getStates();
        return view('cities.edit', compact(['city','countries','states']));
    }
    public function view(City $city)
    {
        $city->load(['country','state']);
        return view('cities.view', compact(['city']));
    }
    public function update(City $city, UpdateCityRequest $updateCityRequest)
    {
        $input = $updateCityRequest->all();
        $updateCity = $this->cityRepository->update($input, $updateCityRequest->id);
        activity()->performedOn($updateCity)->causedBy(getLoggedInUser())
            ->useLog('City Updated')->log($updateCity->name . ' City updated.');
        return $this->sendSuccess(__('messages.cities.saved'));
    }
}
