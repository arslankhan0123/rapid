<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Queries\BankDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\LeaveRequest;
use App\Models\Leave;
use App\Http\Requests\UpdateBankRequest;
use App\Repositories\BankRepository;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use App\Models\Bank;

class BankController extends AppBaseController
{
    /**
     * @var BankRepository;
     */
    private $bankRepository;
    public function __construct(BankRepository $bankRepo)
    {
        $this->bankRepository = $bankRepo;
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
            return DataTables::of((new BankDataTable())->get($request->all()))->make(true);
        }
        return view('banks.index');
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(BankRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->bankRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Bank created.')
                ->log($designation->name);
            Flash::success(__('messages.banks.saved'));
            return $this->sendResponse($designation, __('messages.banks.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(Bank $bank)
    {

        try {
            $bank->delete();
            activity()->performedOn($bank)->causedBy(getLoggedInUser())
                ->useLog('Bank deleted.')->log($bank->name . 'Bank deleted.');
            return $this->sendSuccess(__('messages.banks.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Bank $bank)
    {

        return view('banks.edit', compact(['bank']));
    }
    public function update(Bank $bank, UpdateBankRequest $updateBankRequest)
    {
        $input = $updateBankRequest->all();
        $designation = $this->bankRepository->update($input, $updateBankRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Bank Updated')->log($designation->name . 'Bank updated.');
        Flash::success(__('messages.banks.saved'));
        return $this->sendSuccess(__('messages.banks.saved'));
    }

    public function view(Bank $bank)
    {

        return view('banks.view', compact(['bank']));
    }
}
