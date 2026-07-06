<?php

namespace App\Http\Controllers;

use App\Queries\AwardDataTable;
use Illuminate\Http\Request;
use App\Repositories\AwardRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\AwardRequest;
use App\Models\SubDepartment;
use App\Http\Requests\UpdateAwardRequest;
use Illuminate\Database\QueryException;
use App\Models\Award;
use Laracasts\Flash\Flash;
use Throwable;
class AwardController extends AppBaseController
{
    /**
     * @var AwardRepository
     */
    private $awardRepository;
    public function __construct(AwardRepository $awardRepo)
    {
        $this->awardRepository = $awardRepo;
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
            return DataTables::of((new AwardDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('awards.index');
    }

    public function create()
    {
        $employees = $this->awardRepository->getEmplyee(); // Retrieves departments as key-value pairs
        return view('awards.create', compact('employees'));
    }

    public function store(AwardRequest $request)
    {

        $input = $request->all();
        try {
            $award = $this->awardRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($award)
                ->useLog('Award created.')
                ->log($award->name . ' Award Created.');
            Flash::success(__('messages.awards.saved'));
            return $this->sendResponse($award, __('messages.awards.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Award $award)
    {

        try {
            $award->delete();
            activity()->performedOn($award)->causedBy(getLoggedInUser())
                ->useLog('Award deleted.')->log($award->name . '  Award deleted.');
            return $this->sendSuccess(__('messages.awards.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Award $award)
    {
        $employees = $this->awardRepository->getEmplyee();
        return view('awards.edit', compact(['employees', 'award']));
    }
    public function update(Award $award, UpdateAwardRequest $updateAwardRequest)
    {

        $input = $updateAwardRequest->all();
        $udpateAward = $this->awardRepository->update($input, $updateAwardRequest->id);
        activity()->performedOn($udpateAward)->causedBy(getLoggedInUser())
            ->useLog('Award Updated')->log($udpateAward->name . ' Award updated.');
        Flash::success(__('messages.awards.saved'));
        return $this->sendSuccess(__('messages.awards.saved'));
    }
    public function view(Award $award)
    {
        $award->load(['employee', 'awardedBy']);
        return view('awards.view', compact([ 'award']));
    }
}
