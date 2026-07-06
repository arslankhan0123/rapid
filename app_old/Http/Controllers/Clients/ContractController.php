<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Setting;
use App\Repositories\ContractRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

/**
 * Class ContractController
 */
class ContractController extends AppBaseController
{
    /**
     * @var ContractRepository
     */
    private $contractRepository;

    public function __construct(ContractRepository $contractRepository)
    {
        $this->contractRepository = $contractRepository;
    }

    /**
     * Display a listing of the Contract.
     *
     * @return Factory|View
     */
    public function index()
    {
        $typeArr = ContractType::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('clients.contracts.index', compact('typeArr'));
    }

    /**
     * @param  Contract  $contract
     * @return Application|Factory|View|RedirectResponse
     */
    public function viewAsCustomer(Contract $contract)
    {
        $customerId = getLoggedInUser()->contact->customer_id;
        $clientContractIds = Contract::whereCustomerId($customerId)->pluck('id')->toArray();

        if (! in_array($contract->id, $clientContractIds)) {
            return redirect()->back();
        }

        $contract = $this->contractRepository->find($contract->id);
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('clients.contracts.view_as_customer', compact('contract', 'settings'));
    }

    /**
     * @param  Contract  $contract
     * @return mixed
     */
    public function convertToPdf(Contract $contract)
    {
        $customerID = getLoggedInUser()->contact->customer_id;
        $clientInvoiceIds = Contract::whereCustomerId($customerID)->pluck('id')->toArray();

        if (! in_array($contract->id, $clientInvoiceIds)) {
            Flash::error(__('messages.seems_you_are_not_allowed_to_access_this_record'));

            return redirect()->back();
        }

        $contract = $this->contractRepository->find($contract->id);
        $settings = Setting::pluck('value', 'key')->toArray();

        $pdf = PDF::loadView('clients.contracts.contract_pdf', compact('contract', 'settings'));

        return $pdf->download($contract->subject.'.pdf');
    }

    /**
     * @return Application|Factory|\Illuminate\View\View
     */
    public function contractSummary()
    {
        $customerId = Auth::user()->contact->customer->id;
        $contracts = Contract::whereCustomerId($customerId)->get();

        $contractTypeIds = [];
        foreach ($contracts as $contract) {
            $contractTypeIds[] = $contract->contract_type_id;
        }

        $contractTypes = ContractType::withCount('contractsCustomer')->whereIn('id', $contractTypeIds)->get();

        return view('clients.contracts.contract_summary', compact('contractTypes'));
    }
}
