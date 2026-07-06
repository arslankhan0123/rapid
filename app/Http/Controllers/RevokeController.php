<?php

namespace App\Http\Controllers;

use App\Models\Termination;
use App\Models\Revoke;
use App\Repositories\RevokeRepository;
use App\Http\Requests\CreateRevokeRequest;
use App\Http\Requests\UpdateRevokeRequest;
use App\Queries\RevokeDataTable;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Flash;
use Activity;
use App\Models\Employee;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Throwable;


class RevokeController extends AppBaseController
{
    private $revokeRepository;

    public function __construct(RevokeRepository $revokeRepo)
    {
        $this->revokeRepository = $revokeRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new RevokeDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('revokes.index');
    }

    public function create()
    {
        // Get all terminations that haven't been revoked yet
        $terminations = Termination::with(['employee', 'employee.department', 'employee.designation'])
            ->whereDoesntHave('revoke')
            ->get();

        return view('revokes.create', compact('terminations'));
    }

    public function store(CreateRevokeRequest $request)
    {
        $input = $request->all();

        // Get the termination
        $termination = Termination::find($input['termination_id']);

        if (!$termination) {
            return $this->sendError(__('messages.terminations.not_found'));
        }

        // Check if termination is already revoked
        if ($termination->isRevoked()) {
            return $this->sendError(__('messages.revokes.already_revoked'));
        }

        $input['employee_id'] = $termination->employee_id;

        try {
            $revoke = $this->revokeRepository->create($input);

            activity()->causedBy(getLoggedInUser())
                ->performedOn($revoke)
                ->useLog('Termination revoked.')
                ->log($termination->employee->name . ' Termination Revoked.');

            Flash::success(__('messages.revokes.saved'));
            return $this->sendResponse($revoke, __('messages.revokes.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function show(Revoke $revoke)
    {
        $revoke->load(['termination', 'employee', 'employee.department', 'employee.designation', 'employee.branch']);
        return view('revokes.show', compact('revoke'));
    }

    public function edit(Revoke $revoke)
    {
        return view('revokes.edit', compact('revoke'));
    }

    public function update(Revoke $revoke, UpdateRevokeRequest $request)
    {
        $input = $request->all();
        $input['status'] = $input['status'] ?? 0;

        try {
            $updatedRevoke = $this->revokeRepository->update($input, $revoke->id);

            activity()->performedOn($updatedRevoke)->causedBy(getLoggedInUser())
                ->useLog('Revoke Updated')->log($updatedRevoke->employee->name . ' Revoke updated.');

            Flash::success(__('messages.revokes.updated'));
            return $this->sendSuccess(__('messages.revokes.updated'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(Revoke $revoke)
    {
        try {
            $employee = Employee::find($revoke->employee_id);

            // If revoke is deleted, check if termination is still active
            if ($revoke->status == 1) {
                $termination = Termination::find($revoke->termination_id);
                if ($termination && $termination->status == 1 && $employee) {
                    $terminationDate = Carbon::parse($termination->date);
                    if ($terminationDate->isToday() || $terminationDate->isPast()) {
                        $employee->status = 0; // inactive
                        $employee->save();
                    }
                }
            }

            $revoke->delete();

            activity()->performedOn($revoke)->causedBy(getLoggedInUser())
                ->useLog('Revoke deleted.')->log($revoke->employee->name . ' Revoke deleted.');

            return $this->sendSuccess(__('messages.revokes.deleted'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
}