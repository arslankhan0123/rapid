<?php

namespace App\Http\Controllers;

use App\Queries\BonusDataTable;
use Illuminate\Http\Request;
use App\Repositories\BonusRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\BonusRequest;
use App\Models\Bonus;
use App\Http\Requests\UpdateBonusRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class BonusController extends AppBaseController
{
    /**
     * @var BonusRepository
     */
    private $bonusRepository;
    public function __construct(BonusRepository $bonusRepos)
    {
        $this->bonusRepository = $bonusRepos;
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
            return DataTables::of((new BonusDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('bonuses.index');
    }

    public function create()
    {
        $employees = $this->bonusRepository->getAllEmployees();
        $types = $this->bonusRepository->getAllowanceTypes();
        $usersBranches = $this->getUsersBranches();
        return view('bonuses.create', compact(['employees', 'types', 'usersBranches']));
    }

    public function store(BonusRequest $request)
    {
        $input = $request->all();
        try {
            $bonus = $this->bonusRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($bonus)
                ->useLog('Bonus created.')
                ->log("Bonuses Created");
            Flash::success(__('messages.bonuses.saved'));
            return $this->sendResponse($bonus, __('messages.bonuses.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Bonus $bonus)
    {
        try {
            $bonus->delete();
            activity()->performedOn($bonus)->causedBy(getLoggedInUser())
                ->useLog('Bonus deleted.')->log($bonus->name . ' deleted.');
            return $this->sendSuccess(__('messages.bonuses.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Bonus $bonus)
    {
        $employees = $this->bonusRepository->getAllEmployees();
        $types = $this->bonusRepository->getAllowanceTypes();
        $usersBranches = $this->getUsersBranches();
        return view('bonuses.edit', compact(['bonus', 'employees', 'types', 'usersBranches']));
    }
    public function update(Bonus $bonus, UpdateBonusRequest $updateBonusRequest)
    {
        $input = $updateBonusRequest->all();
        $designation = $this->bonusRepository->update($input, $updateBonusRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Department Updated')->log($designation->name . 'Bonuses updated.');
        Flash::success(__('messages.bonuses.saved'));
        return $this->sendSuccess(__('messages.bonuses.saved'));
    }
    public function view(Bonus $bonus)
    {
        $bonus->load(['employee', 'bonusTypes','employee.branch']);
        return view('bonuses.view', compact(['bonus']));
    }
}
