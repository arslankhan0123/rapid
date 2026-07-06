<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Models\Reminder;
use App\Queries\ReminderDataTable;
use App\Repositories\ReminderRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ReminderController extends AppBaseController
{
    /** @var ReminderRepository */
    private $reminderRepository;

    public function __construct(ReminderRepository $reminderRepository)
    {
        $this->reminderRepository = $reminderRepository;
    }

    /**
     * Display a listing of the Reminder.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ReminderDataTable())->get($request->only([
                'module_id', 'owner_id',
            ])))->make(true);
        }

        return view('reminders.index');
    }

    /**
     * Store a newly created Reminder in storage.
     *
     * @param  CreateReminderRequest  $request
     * @return JsonResponse
     */
    public function store(CreateReminderRequest $request)
    {
        $input = $request->all();
        $this->reminderRepository->store($input);

        return $this->sendSuccess(__('messages.reminder.reminder_saved_successfully'));
    }

    /**
     * Show the form for editing the specified Reminder.
     *
     * @param  Reminder  $reminder
     * @return JsonResponse
     */
    public function edit(Reminder $reminder)
    {
        return $this->sendResponse($reminder, 'Reminder retrieved successfully.');
    }

    /**
     * Update the specified Reminder in storage.
     *
     * @param  UpdateReminderRequest  $request
     * @param  Reminder  $reminder
     * @return JsonResponse
     */
    public function update(UpdateReminderRequest $request, Reminder $reminder)
    {
        $input = $request->all();
        $input['status'] = (isset($input['status'])) ? 1 : 0;
        $this->reminderRepository->update($input, $reminder);

        return $this->sendSuccess(__('messages.reminder.reminder_updated_successfully'));
    }

    /**
     * Remove the specified Reminder from storage.
     *
     * @param  Reminder  $reminder
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Reminder $reminder)
    {
        activity()->performedOn($reminder)->causedBy(getLoggedInUser())
            ->useLog('Reminder deleted.')->log(html_entity_decode($reminder->description).' Reminder deleted.');

        $reminder->delete();

        return $this->sendSuccess('Reminder deleted successfully.');
    }
}
