<?php

namespace App\Http\Controllers;

use App\Queries\AreaDataTable;
use Illuminate\Http\Request;
use App\Repositories\AreaRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\AreaRequest;
use App\Models\State;
use App\Http\Requests\UpdateAreaRequest;
use Illuminate\Database\QueryException;
use Throwable;
use App\Models\Area;

class AreaController extends AppBaseController
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;
    public function __construct(AreaRepository $areaRepos)
    {
        $this->areaRepository = $areaRepos;
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
            return DataTables::of((new AreaDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('areas.index');
    }

    public function create()
    {
        $countries=$this->areaRepository->getCountries();
        $cities=$this->areaRepository->getCities();
        $states=$this->areaRepository->getStates();
        return view('areas.create',compact(['countries', 'states','cities']));
    }

    public function store(AreaRequest $request)
    {

        $input = $request->all();
        try {
            $area = $this->areaRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($area)
                ->useLog('Area created.')
                ->log($area->name . ' Area Created');
            session()->flash('success',__('messages.areas.saved'));
            return $this->sendResponse($area, __('messages.areas.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Area $area)
    {
        try {
            $area->delete();
            activity()->performedOn($area)->causedBy(getLoggedInUser())
                ->useLog('Area deleted.')->log($area->name . ' Area deleted.');
            return $this->sendSuccess(__('messages.areas.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Area $area)
    {
        $countries = $this->areaRepository->getCountries();
        $states = $this->areaRepository->getStates();
        $cities = $this->areaRepository->getCities();
        return view('areas.edit', compact(['area', 'countries', 'states', 'cities']));
    }
    public function view(Area $area)
    {

        $area->load(['country','city','state']);
        return view('areas.view', compact(['area']));
    }
    public function update(Area $area, UpdateAreaRequest $updateAreaRequest)
    {
        $input = $updateAreaRequest->all();
        $area = $this->areaRepository->update($input, $updateAreaRequest->id);
        activity()->performedOn($area)->causedBy(getLoggedInUser())
            ->useLog('Area Updated')->log($area->name . ' Area updated.');
        session()->flash('success', __('messages.areas.saved'));
        return $this->sendSuccess(__('messages.areas.saved'));
    }
}
