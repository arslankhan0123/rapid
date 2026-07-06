<?php

namespace App\Http\Controllers;

use App\Queries\NoticeDataTable;
use Illuminate\Http\Request;
use App\Repositories\NoticeRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use Illuminate\Database\QueryException;
use App\Http\Requests\NoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Models\Notice;

class NoticeController extends AppBaseController
{
    /**
     * @var NoticeRepository
     */
    private $noticeRepository;
    public function __construct(NoticeRepository $noticeRep)
    {
        $this->noticeRepository = $noticeRep;
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
            return DataTables::of((new NoticeDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('notices.index');
    }

    function all_notices(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new NoticeDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('notices.notices');
    }
    public function create()
    {
        return view('notices.create');
    }
    public function store(NoticeRequest $request)
    {

        $input = $request->all();
        try {
            $designation = $this->noticeRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Department created.')
                ->log($designation->name . ' Notice.');
            return $this->sendResponse($designation, __('messages.notices.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Notice $notice)
    {
        try {
            $notice->delete();
            activity()->performedOn($notice)->causedBy(getLoggedInUser())
                ->useLog('Notice deleted.')->log($notice->name . 'Notice deleted.');
            return $this->sendSuccess(__('messages.notices.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Notice $notice)
    {
        return view('notices.edit', compact(['notice']));
    }
    public function update(Notice $notice, UpdateNoticeRequest $updateNoticeRequest)
    {
        $input = $updateNoticeRequest->all();
        if (!isset($input['show'])) {
            $input['show'] = 0;
        }

        $designation = $this->noticeRepository->update($input, $updateNoticeRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Notic Updated')->log($designation->title . 'Notice updated.');
        return $this->sendSuccess(__('messages.notices.saved'));
    }
}
