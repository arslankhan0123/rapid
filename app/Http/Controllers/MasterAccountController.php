<?php

namespace App\Http\Controllers;

use App\Models\MasterAccount;
use App\Repositories\MasterAccountRepository;
use App\Http\Requests\CreateMasterAccountRequest;
use App\Http\Requests\UpdateMasterAccountRequest;
use Illuminate\Http\Request;
use Flash;
use Yajra\DataTables\DataTables;
use Activity;
use Illuminate\Database\QueryException;
use App\Queries\MasterAccountDataTable;

class MasterAccountController extends AppBaseController
{
    private $masterAccountRepository;

    public function __construct(MasterAccountRepository $masterAccountRepo)
    {
        $this->masterAccountRepository = $masterAccountRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // return DataTables::of((new MasterAccountDataTable())->get($request->only(['group'])))->make(true);
            return DataTables::of((new MasterAccountDataTable())->get($request->all()))->make(true);
        }
        return view('master_accounts.index');
    }

    public function create()
    {
        return view('master_accounts.create');
    }

    public function store(CreateMasterAccountRequest $request)
    {
        $input = $request->all();
        $input['status'] = $input['status'] ?? true;

        try {
            $masterAccount = $this->masterAccountRepository->create($input);

            activity()->causedBy(getLoggedInUser())
                ->performedOn($masterAccount)
                ->useLog('Master Account created.')
                ->log($masterAccount->name . ' Master Account Created.');

            Flash::success(__('messages.master_accounts.saved'));
            return $this->sendResponse($masterAccount, __('messages.master_accounts.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function show(MasterAccount $masterAccount)
    {
        return view('master_accounts.show', compact('masterAccount'));
    }

    public function edit(MasterAccount $masterAccount)
    {
        return view('master_accounts.edit', compact('masterAccount'));
    }

    public function update(MasterAccount $masterAccount, UpdateMasterAccountRequest $request)
    {
        $input = $request->all();
        $input['status'] = $input['status'] ?? true;

        try {
            $updatedMasterAccount = $this->masterAccountRepository->update($input, $masterAccount->id);

            activity()->performedOn($updatedMasterAccount)->causedBy(getLoggedInUser())
                ->useLog('Master Account Updated')->log($updatedMasterAccount->name . ' Master Account updated.');

            Flash::success(__('messages.master_accounts.updated'));
            return $this->sendSuccess(__('messages.master_accounts.updated'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(MasterAccount $masterAccount)
    {
        try {
            $masterAccount->delete();

            activity()->performedOn($masterAccount)->causedBy(getLoggedInUser())
                ->useLog('Master Account deleted.')->log($masterAccount->name . ' Master Account deleted.');

            return $this->sendSuccess(__('messages.master_accounts.deleted'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
}
