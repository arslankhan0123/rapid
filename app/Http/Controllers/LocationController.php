<?php

namespace App\Http\Controllers;

use App\Queries\LocationDataTable;
use Illuminate\Http\Request;
use App\Repositories\LocationRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Http\Requests\UpdateLocationRequest;
use Illuminate\Database\QueryException;
use Throwable;

class LocationController extends AppBaseController
{
    /**
     * @var LocationRepository
     */
    private $locationRepository;
    public function __construct(LocationRepository $locationRepo)
    {
        $this->locationRepository = $locationRepo;
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
            return DataTables::of((new LocationDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('locations.index');
    }

    public function create()
    {
        $countries = $this->locationRepository->getCountries();
        return view('locations.create', compact(['countries']));
    }

    public function store(LocationRequest $request)
    {
        $input = $request->all();
        try {
            $location = $this->locationRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($location)
                ->useLog('Location created.')
                ->log($location->name . ' Location Created');
            return $this->sendResponse($location, __('messages.locations.saved') ?? 'Location saved successfully.');
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function destroy(Location $location)
    {
        try {
            $location->delete();
            activity()->performedOn($location)->causedBy(getLoggedInUser())
                ->useLog('Location deleted.')->log($location->name . ' Location deleted.');
            return $this->sendSuccess(__('messages.locations.delete') ?? 'Location deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Location $location)
    {
        $countries = $this->locationRepository->getCountries();
        return view('locations.edit', compact(['location', 'countries']));
    }
    
    public function view(Location $location)
    {
        $location->load('country');
        return view('locations.view', compact(['location']));
    }
    
    public function update(Location $location, UpdateLocationRequest $updateLocationRequest)
    {
        $input = $updateLocationRequest->all();
        $location = $this->locationRepository->update($input, $updateLocationRequest->id);
        activity()->performedOn($location)->causedBy(getLoggedInUser())
            ->useLog('Location Updated')->log($location->name . ' Location updated.');
        return $this->sendSuccess(__('messages.locations.saved') ?? 'Location updated successfully.');
    }
}
