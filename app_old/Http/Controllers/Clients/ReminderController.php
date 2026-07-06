<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Queries\Clients\ReminderDataTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 * Class ReminderController
 */
class ReminderController extends AppBaseController
{
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
            return DataTables::of((new ReminderDataTable())->get($request->only(['owner_id'])))->make(true);
        }

        return view('clients.reminders.index');
    }
}
