<?php

namespace App\Http\Controllers;

use App\Queries\CurrencyDataTable;
use Illuminate\Http\Request;
use App\Repositories\CurrencyRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CurrencyReqeust;
use App\Models\Currency;
use App\Http\Requests\UpdateCurrencyRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class CurrencyController extends AppBaseController
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;
    public function __construct(CurrencyRepository $currencyRepo)
    {
        $this->currencyRepository = $currencyRepo;
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
            return DataTables::of((new CurrencyDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('currencies.index');
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(CurrencyReqeust $request)
    {

        $input = $request->all();
        try {
            $currency = $this->currencyRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($currency)
                ->useLog('Currency created.')
                ->log($currency->name . ' Currency Created');
            Flash::success(__('messages.currencies.saved'));
            return $this->sendResponse($currency, __('messages.currencies.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Currency $currency)
    {
        try {
            $currency->delete();
            activity()->performedOn($currency)->causedBy(getLoggedInUser())
                ->useLog('Currency deleted.')->log($currency->name . ' Currency deleted.');
            return $this->sendSuccess(__('messages.currencies.saved'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact(['currency']));
    }
    public function view(Currency $currency)
    {
        return view('currencies.view', compact(['currency']));
    }
    public function update(Currency $currency, UpdateCurrencyRequest $updateCurrencyRequest)
    {
        $input = $updateCurrencyRequest->all();
        $updateCurrency = $this->currencyRepository->update($input, $updateCurrencyRequest->id);
        activity()->performedOn($updateCurrency)->causedBy(getLoggedInUser())
            ->useLog('Currency Updated')->log($updateCurrency->name . ' Currency updated.');
        Flash::success(__('messages.currencies.saved'));
        return $this->sendSuccess(__('messages.currencies.saved'));
    }
}
