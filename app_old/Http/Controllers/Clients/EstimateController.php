<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Models\Estimate;
use App\Models\Setting;
use App\Repositories\EstimateRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

/**
 * Class EstimateController
 */
class EstimateController extends AppBaseController
{
    /**
     * @var EstimateRepository
     */
    private $estimateRepository;

    public function __construct(EstimateRepository $estimateRepository)
    {
        $this->estimateRepository = $estimateRepository;
    }

    /**
     * Display a listing of the Estimate.
     *
     * @return Factory|View
     */
    public function index()
    {
        $estimateStatusCount = $this->estimateRepository->getEstimatesStatusCount(getLoggedInUser()->contact->customer_id);
        $estimateStatus = Estimate::CLIENT_STATUS;

        return view('clients.estimates.index', compact('estimateStatusCount', 'estimateStatus'));
    }

    /**
     * @param  Estimate  $estimate
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function changeStatus(Estimate $estimate, Request $request): RedirectResponse
    {
        $statusUpdate = $this->estimateRepository->changeStatus($estimate->id, $request->status);

        return redirect()->route('clients.estimates.view-as-customer', $estimate->id);
    }

    /**
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function viewAsCustomer(Estimate $estimate)
    {
        $estimate = $this->estimateRepository->getEstimateDetailClient($estimate->id);

        $settings = Setting::pluck('value', 'key')->toArray();

        $totalPaid = 0;

        return view('clients.estimates.view_as_customer', compact('estimate', 'totalPaid', 'settings'));
    }

    /**
     * @param  Estimate  $estimate
     * @return mixed
     */
    public function convertToPDF(Estimate $estimate)
    {
        $customerID = getLoggedInUser()->contact->customer_id;
        $clientInvoiceIds = Estimate::whereCustomerId($customerID)->pluck('id')->toArray();

        if (! in_array($estimate->id, $clientInvoiceIds)) {
            Flash::error(__('messages.seems_you_are_not_allowed_to_access_this_record'));

            return redirect()->back();
        }

        $estimate = $this->estimateRepository->getSyncForEstimateDetail($estimate->id);
        $settings = Setting::pluck('value', 'key')->toArray();

        $pdf = PDF::loadView('clients.estimates.estimate_pdf', compact(['estimate', 'settings']));

        return $pdf->download(__('messages.estimate.estimate_prefix').$estimate->estimate_number.'.pdf');
    }
}
