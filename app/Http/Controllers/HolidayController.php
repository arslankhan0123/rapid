<?php

namespace App\Http\Controllers;

use App\Queries\HolidayDataTable;
use Illuminate\Http\Request;
use App\Repositories\HolidayRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\HolidayRequest;
use App\Models\Holiday;
use App\Http\Requests\UpdateHolidayRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class HolidayController extends AppBaseController
{
    /**
     * @var HolidayRepository
     */
    private $holidayRepository;
    public function __construct(HolidayRepository $holidayRepo)
    {
        $this->holidayRepository = $holidayRepo;
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
            return DataTables::of((new HolidayDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('holidays.index');
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(HolidayRequest $request)
    {
        $input = $request->all();

        $validator = Holiday::validateHolidayDates($input);

        if ($validator->fails()) {
            // Get all error messages as a single string
            $allErrors = $validator->errors()->all();
            $errorMessage = implode(' ', $allErrors);
            return $this->sendError("Holiday conflict: $errorMessage");
        }
        try {
            $holiday = $this->holidayRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($holiday)
                ->useLog('Holiday created.')
                ->log($holiday->name);
            Flash::success(__('messages.holidays.saved'));
            return $this->sendResponse($holiday->name, __('messages.holidays.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Holiday $holiday)
    {
        try {
            $holiday->delete();
            activity()->performedOn($holiday)->causedBy(getLoggedInUser())
                ->useLog('Holiday deleted.')->log($holiday->name . ' deleted.');
            return $this->sendSuccess(__('messages.holidays.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Holiday $holiday)
    {
        return view('holidays.edit', compact(['holiday']));
    }
    public function update(Holiday $holiday, UpdateHolidayRequest $updateHolidayRequest)
    {
        $input = $updateHolidayRequest->all();

        $updateHoliday = $this->holidayRepository->update($input, $updateHolidayRequest->id);
        activity()->performedOn($updateHoliday)->causedBy(getLoggedInUser())
            ->useLog('Holiday Updated')->log($updateHoliday->name . 'Holiday updated.');
        Flash::success(__('messages.holidays.saved'));
        return $this->sendSuccess(__('messages.holidays.saved'));
    }
    public function view(Holiday $holiday)
    {
        return view('holidays.view', compact(['holiday']));
    }
}
