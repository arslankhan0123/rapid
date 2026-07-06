<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Task;
use App\Repositories\ContractRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class ContractController extends AppBaseController
{
    /** @var ContractRepository */
    private $contractRepository;

    public function __construct(ContractRepository $contractRepo)
    {
        $this->contractRepository = $contractRepo;
    }

    /**
     * Display a listing of the Contract.
     *
     * @return Factory|View
     */
    public function index()
    {
        $typeArr = ContractType::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('contracts.index', compact('typeArr'));
    }

    /**
     * Show the form for creating a new Contract.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null)
    {
        $contractType = $this->contractRepository->getContractType();
        $customer = $this->contractRepository->getCustomer();

        return view('contracts.create', compact('contractType', 'customer', 'customerId'));
    }

    /**
     * Store a newly created Contract in storage.
     *
     * @param  CreateContractRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateContractRequest $request)
    {
        $input = $request->all();
        $input['contract_value'] = removeCommaFromNumbers($input['contract_value']);
        $input['contract_value'] = (! empty($input['contract_value'])) ? $input['contract_value'] : null;
        $contract = $this->contractRepository->create($input);

        activity()->performedOn($contract)->causedBy(getLoggedInUser())
            ->useLog('New Contract created.')->log($contract->subject.' Contract created.');

        Flash::success(__('messages.contract.contracts_saved_successfully'));

        return redirect(route('contracts.index'));
    }

    /**
     * Display the specified Contract.
     *
     * @param  Contract  $contract
     * @return Factory|View
     */
    public function show(Contract $contract)
    {
        $status = Task::STATUS;
        $priorities = Task::PRIORITY;
        $groupName = (request('group') == null) ? 'contract_details' : (request('group'));

        return view("contracts.views.$groupName", compact('contract', 'status', 'priorities', 'groupName'));
    }

    /**
     * Show the form for editing the specified Contract.
     *
     * @param  Contract  $contract
     * @return Factory|View
     */
    public function edit(Contract $contract)
    {
        $contractType = $this->contractRepository->getContractType();
        $customer = $this->contractRepository->getCustomer();

        return view('contracts.edit', compact('contract', 'contractType', 'customer'));
    }

    /**
     * Update the specified Contract in storage.
     *
     * @param  UpdateContractRequest  $request
     * @param  Contract  $contract
     * @return RedirectResponse|Redirector
     */
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $input = $request->all();
        $input['contract_value'] = removeCommaFromNumbers($input['contract_value']);
        $input['contract_value'] = (! empty($input['contract_value'])) ? $input['contract_value'] : null;
        $contract = $this->contractRepository->update($input, $contract->id);

        activity()->performedOn($contract)->causedBy(getLoggedInUser())
            ->useLog('Contract updated.')->log($contract->subject.' Contract updated.');

        Flash::success(__('messages.contract.contracts_updated_successfully'));

        return redirect(route('contracts.index'));
    }

    /**
     * Remove the specified Contract from storage.
     *
     * @param  Contract  $contract
     * @return Response
     *
     * @throws Exception
     */
    public function destroy(Contract $contract)
    {
        activity()->performedOn($contract)->causedBy(getLoggedInUser())
            ->useLog('Contract deleted.')->log($contract->subject.' Contract deleted.');

        $contract->delete();

        return $this->sendSuccess('Contract deleted successfully.');
    }

    /**
     * @return Application|Factory|View
     */
    public function contractSummary()
    {
        $contractForTypes = ContractType::withCount('contracts')->get();
        $contractForValues = ContractType::with('contracts')->get();

        foreach ($contractForValues as $contractForValue) {
            $contractForValue['total_contract_value'] = $contractForValue->contracts->sum('contract_value');
        }

        return view('contracts.contract_summary.index', compact('contractForTypes', 'contractForValues'));
    }
}
