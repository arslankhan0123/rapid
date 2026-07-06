<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePredefinedReplyRequest;
use App\Http\Requests\UpdatePredefinedReplyRequest;
use App\Models\PredefinedReply;
use App\Repositories\PredefinedReplyRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PredefinedReplyController extends AppBaseController
{
    /** @var PredefinedReplyRepository */
    private $predefinedReplyRepository;

    public function __construct(PredefinedReplyRepository $predefinedReplyRepo)
    {
        $this->predefinedReplyRepository = $predefinedReplyRepo;
    }

    /**
     * Display a listing of the PredefinedReply.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('predefined_replies.index');
    }

    /**
     *  Store a newly created PredefinedReply in storage.
     *
     * @param  CreatePredefinedReplyRequest  $request
     * @return JsonResponse
     */
    public function store(CreatePredefinedReplyRequest $request)
    {
        $input = $request->all();
        $predefinedReply = $this->predefinedReplyRepository->create($input);
        activity()->performedOn($predefinedReply)->causedBy(getLoggedInUser())
            ->useLog('New Predefined Reply created.')->log($predefinedReply->name.' Predefined Reply created.');

        return $this->sendResponse($predefinedReply, __('messages.predefined_reply.predefined_reply_saved_successfully'));
    }

    /**
     * Show the form for editing the specified PredefinedReply.
     *
     * @param  PredefinedReply  $predefinedReply
     * @return JsonResponse
     */
    public function edit(PredefinedReply $predefinedReply)
    {
        return $this->sendResponse($predefinedReply, 'Predefined Reply retrieved Successfully.');
    }

    /**
     * Update the specified PredefinedReply in storage.
     *
     * @param  UpdatePredefinedReplyRequest  $request
     * @param  PredefinedReply  $predefinedReply
     * @return JsonResponse
     */
    public function update(UpdatePredefinedReplyRequest $request, PredefinedReply $predefinedReply)
    {
        $input = $request->all();
        $predefinedReply = $this->predefinedReplyRepository->update($input, $predefinedReply->id);
        activity()->performedOn($predefinedReply)->causedBy(getLoggedInUser())
            ->useLog('Predefined Reply updated.')->log($predefinedReply->name.' Predefined Reply updated.');

        return $this->sendSuccess(__('messages.predefined_reply.predefined_reply_updated_successfully'));
    }

    /**
     * @param  PredefinedReply  $predefinedReply
     * @return mixed
     */
    public function show(PredefinedReply $predefinedReply)
    {
        return $this->sendResponse($predefinedReply, 'Predefined Replay retrieved successfully.');
    }

    /**
     * Remove the specified PredefinedReply from storage.
     *
     * @param  PredefinedReply  $predefinedReply
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(PredefinedReply $predefinedReply)
    {
        $predefinedReply = PredefinedReply::findOrFail($predefinedReply->id)->delete();

        return $this->sendSuccess('Predefined Reply deleted successfully.');
    }
}
