<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Models\Proposal;
use App\Models\Setting;
use App\Repositories\ProposalRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

/**
 * Class ProposalController
 */
class ProposalController extends AppBaseController
{
    /**
     * @var ProposalRepository
     */
    private $proposalRepository;

    public function __construct(ProposalRepository $proposalRepository)
    {
        $this->proposalRepository = $proposalRepository;
    }

    /**
     * Display a listing of the Proposal.
     *
     * @return Factory|View
     */
    public function index()
    {
        $proposalStatusCount = $this->proposalRepository->getProposalsStatusCount(getLoggedInUser()->contact->customer_id);
        $proposalStatus = Proposal::CLIENT_STATUS;

        return view('clients.proposals.index', compact('proposalStatusCount', 'proposalStatus'));
    }

    /**
     * @param  Proposal  $proposal
     * @return Factory|View
     */
    public function viewAsCustomer(Proposal $proposal)
    {
        $proposal = $this->proposalRepository->getProposalDetailClient($proposal);

        $settings = Setting::pluck('value', 'key')->toArray();

        $totalPaid = 0;
        $proposalStatus = Proposal::STATUS;

        return view('clients.proposals.view_as_customer',
            compact('proposal', 'totalPaid', 'proposalStatus', 'settings'));
    }

    /**
     * @param  Proposal  $proposal
     * @return mixed
     */
    public function covertToPdf(Proposal $proposal)
    {
        $ownerID = getLoggedInUser()->contact->customer_id;
        $clientInvoiceIds = Proposal::whereOwnerId($ownerID)->pluck('id')->toArray();

        if (! in_array($proposal->id, $clientInvoiceIds)) {
            Flash::error(__('messages.seems_you_are_not_allowed_to_access_this_record'));

            return redirect()->back();
        }

        $proposal = $this->proposalRepository->getSyncListForProposalDetail($proposal->id);

        $totalPaid = 0;

        $settings = Setting::pluck('value', 'key')->toArray();

        $pdf = PDF::loadView('clients.proposals.proposal_pdf', compact(['proposal', 'settings', 'totalPaid']));

        return $pdf->download(__('messages.proposal.proposal_prefix').$proposal->proposal_number.'.pdf');
    }

    /**
     * @param  Proposal  $proposal
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function changeStatus(Proposal $proposal, Request $request)
    {
        $status = $request->status;
        $changeStatus = $this->proposalRepository->changeProposalStatus($proposal->id, $status);

        return redirect()->route('clients.proposals.view-as-customer', $proposal->id);
    }
}
