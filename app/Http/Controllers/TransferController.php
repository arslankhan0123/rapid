<?php

namespace App\Http\Controllers;

use App\Queries\TransferDataTable;
use Illuminate\Http\Request;
use App\Repositories\TransferRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\TransferRequest;
use App\Models\Bonus;
use App\Http\Requests\UpdateTransferRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\Transfer;

class TransferController extends AppBaseController
{
    /**
     * @var TransferRepository
     */
    private $transferRepository;
    public function __construct(TransferRepository $transferRepo)
    {
        $this->transferRepository = $transferRepo;
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
            return DataTables::of((new TransferDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('transfers.index',compact('usersBranches'));
    }

    public function create()
    {
        $employees = $this->transferRepository->getAllEmployees();
        $usersBranches = $this->getUsersBranches();
        return view('transfers.create', compact(['employees', 'usersBranches']));
    }

    public function store(TransferRequest $request)
    {
        $input = $request->all();
        try {
            $bonus = $this->transferRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($bonus)
                ->useLog('Employee Transferred')
                ->log("Employee Transferred");
            Flash::success(__('messages.transfers.saved'));
            return $this->sendResponse($bonus, __('messages.transfers.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Transfer $transfer)
    {
        try {
            $transfer->delete();
            activity()->performedOn($transfer)->causedBy(getLoggedInUser())
                ->useLog('Transferred deleted.')->log(' deleted.');
            return $this->sendSuccess(__('messages.transfers.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Transfer $transfer)
    {
        // dd($transfer->toArray());
        $employees = $this->transferRepository->getAllEmployees();
        $usersBranches = $this->getUsersBranches();
        return view('transfers.edit', compact(['transfer', 'employees', 'usersBranches']));
    }
    public function update(Transfer $transfer, UpdateTransferRequest $updateTransferRequest)
    {
        $input = $updateTransferRequest->all();
        $designation = $this->transferRepository->update_transfer($input, $updateTransferRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Employee Transfer  Updated')->log($designation->name . 'Employee Transfer  Updated.');
        Flash::success(__('messages.transfers.saved'));
        return $this->sendSuccess(__('messages.transfers.saved'));
    }
    public function view(Transfer $transfer)
    {
        $transfer->load(['employee', 'fromBranch','toBranch','branch']);
        return view('transfers.view', compact(['transfer']));
    }
}
