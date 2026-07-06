<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxRateRequest;
use App\Http\Requests\UpdateTaxRateRequest;
use App\Models\TaxRate;
use App\Repositories\TaxRateRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TaxRateController extends AppBaseController
{
    /** @var TaxRateRepository */
    private $taxRateRepository;

    public function __construct(TaxRateRepository $taxRateRepo)
    {
        $this->taxRateRepository = $taxRateRepo;
    }

    /**
     * Display a listing of the TaxRate.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('tax_rates.index');
    }

    /**
     * Store a newly created TaxRate in storage.
     *
     * @param  CreateTaxRateRequest  $request
     * @return JsonResponse
     */
    public function store(CreateTaxRateRequest $request)
    {
        $input = $request->all();

        $taxRate = $this->taxRateRepository->create($input);

        activity()->performedOn($taxRate)->causedBy(getLoggedInUser())
            ->useLog('New Tax Rate created.')->log($taxRate->name.' Tax Rate created.');

        return $this->sendSuccess(__('messages.tax_rate.tax_rate_saved_successfully'));
    }

    /**
     * Show the form for editing the specified TaxRate.
     *
     * @param  TaxRate  $taxRate
     * @return JsonResponse
     */
    public function edit(TaxRate $taxRate)
    {
        return $this->sendResponse($taxRate, 'Tax Rate retrieved successfully.');
    }

    /**
     * Update the specified TaxRate in storage.
     *
     * @param  TaxRate  $taxRate
     * @param  UpdateTaxRateRequest  $request
     * @return JsonResponse
     */
    public function update(TaxRate $taxRate, UpdateTaxRateRequest $request)
    {
        $input = $request->all();

        $taxRate = $this->taxRateRepository->update($input, $taxRate->id);

        activity()->performedOn($taxRate)->causedBy(getLoggedInUser())
            ->useLog('Tax Rate updated.')->log($taxRate->name.' Tax Rate updated.');

        return $this->sendSuccess(__('messages.tax_rate.tax_rate_updated_successfully'));
    }

    /**
     * Remove the specified TaxRate from storage.
     *
     * @param  TaxRate  $taxRate
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return $this->sendSuccess('Tax Rate deleted successfully.');
    }
}
