<?php

namespace App\Http\Controllers;

use App\Queries\RentalDataTable;
use Illuminate\Http\Request;
use App\Repositories\RentalRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\RentalRequest;
use App\Models\Rental;
use App\Http\Requests\UpdateRentalRequest;
use Illuminate\Database\QueryException;
use App\Models\TaxRate;
use Laracasts\Flash\Flash;
use Throwable;

class RentalController extends AppBaseController
{
    /**
     * @var RentalRepository;
     */
    private $rentalRepository;
    public function __construct(RentalRepository $rentalRepo)
    {
        $this->rentalRepository = $rentalRepo;
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
            return DataTables::of((new RentalDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('rentals.index');
    }

    public function create()
    {
        $suppliers = $this->rentalRepository->getSuppliers();
        $allTaxes = $this->rentalRepository->getTaxRates(); // Retrieves departments as key-value pairs
        $taxRates= $allTaxes->pluck('name', 'id')->map(function ($name, $id) {
            $rate = TaxRate::find($id)->tax_rate; // Retrieve the rate
            return "{$name} ({$rate}%)";
        });
        return view('rentals.create', compact(['suppliers', 'taxRates', 'allTaxes']));
    }

    public function store(RentalRequest $request)
    {

        $input = $request->all();
        try {
            $rental = $this->rentalRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($rental)
                ->useLog('Rental created.')
                ->log(' Rental Created.');
            Flash::success(__('messages.rentals.saved'));
            return $this->sendResponse($rental, __('messages.rentals.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Rental $rental)
    {

        try {
            $rental->delete();
            activity()->performedOn($rental)->causedBy(getLoggedInUser())
                ->useLog('Rental deleted.')->log(' Rental  deleted.');
            return $this->sendSuccess(__('messages.rentals.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Rental $rental)
    {

        $suppliers = $this->rentalRepository->getSuppliers();
        $allTaxes = $this->rentalRepository->getTaxRates(); // Retrieves departments as key-value pairs
        $taxRates = $allTaxes->pluck('name', 'id')->map(function ($name, $id) {
            $rate = TaxRate::find($id)->tax_rate; // Retrieve the rate
            return "{$name} ({$rate}%)";
        });
        return view('rentals.edit', compact(['suppliers', 'rental', 'taxRates', 'allTaxes']));
    }
    public function update(Rental $rental, UpdateRentalRequest $updateRentalRequest)
    {
        $input = $updateRentalRequest->all();
        $updateRetirement = $this->rentalRepository->update($input, $updateRentalRequest->id);
        activity()->performedOn($updateRetirement)->causedBy(getLoggedInUser())
            ->useLog('Rental Updated')->log( ' Rental updated.');
        Flash::success(__('messages.rentals.saved'));
        return $this->sendSuccess(__('messages.rentals.saved'));
    }
    public function view(Rental $rental)
    {
        $rental->load('supplier');
        return view('rentals.view', compact([ 'rental']));
    }
}
