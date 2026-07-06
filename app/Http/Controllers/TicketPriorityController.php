<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketPriorityRequest;
use App\Http\Requests\UpdateTicketPriorityRequest;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Repositories\TicketPriorityRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TicketPriorityController extends AppBaseController
{
    /** @var TicketPriorityRepository */
    private $ticketPriorityRepository;

    public function __construct(TicketPriorityRepository $ticketPriorityRepo)
    {
        $this->ticketPriorityRepository = $ticketPriorityRepo;
    }

    /**
     * Display a listing of the TicketPriority.
     *
     * @return Factory|View
     */
    public function index()
    {
        $statusArr = TicketPriority::STATUS_ARR;

        return view('ticket_priorities.index', compact('statusArr'));
    }

    /**
     * Store a newly created TicketPriority in storage.
     *
     * @param  CreateTicketPriorityRequest  $request
     * @return JsonResponse
     */
    public function store(CreateTicketPriorityRequest $request)
    {
        $input = $request->all();
        $input['status'] = ! isset($input['status']) ? false : true;
        $ticketPriority = $this->ticketPriorityRepository->create($input);
        activity()->performedOn($ticketPriority)->causedBy(getLoggedInUser())
            ->useLog('New Ticket Priority created.')->log($ticketPriority->name.' Ticket Priority created.');

        return $this->sendResponse($ticketPriority, __('messages.ticket_priority.ticket_priority_saved_successfully'));
    }

    /**
     * Show the form for editing the specified TicketPriority.
     *
     * @param  TicketPriority  $ticketPriority
     * @return JsonResponse
     */
    public function edit(TicketPriority $ticketPriority)
    {
        return $this->sendResponse($ticketPriority, 'Ticket Priority retrieved successfully.');
    }

    /**
     * Update the specified TicketPriority in storage.
     *
     * @param  UpdateTicketPriorityRequest  $request
     * @param  TicketPriority  $ticketPriority
     * @return JsonResponse
     */
    public function update(UpdateTicketPriorityRequest $request, TicketPriority $ticketPriority)
    {
        $input = $request->all();
        $input['status'] = ! isset($input['status']) ? false : true;
        $ticketPriority = $this->ticketPriorityRepository->update($input, $ticketPriority->id);
        activity()->performedOn($ticketPriority)->causedBy(getLoggedInUser())
            ->useLog('Ticket Priority updated.')->log($ticketPriority->name.' Ticket Priority updated.');

        return $this->sendSuccess(__('messages.ticket_priority.ticket_priority_updated_successfully'));
    }

    /**
     * Remove the specified TicketPriority from storage.
     *
     * @param  TicketPriority  $ticketPriority
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(TicketPriority $ticketPriority)
    {
        $ticketPriorityId = Ticket::where('priority_id', '=', $ticketPriority->id)->exists();

        if ($ticketPriorityId) {
            return $this->sendError(__('messages.ticket_priority.ticket_priority_used_somewhere_else'));
        }

        activity()->performedOn($ticketPriority)->causedBy(getLoggedInUser())
            ->useLog('Ticket Priority deleted.')->log($ticketPriority->name.' Ticket Priority deleted.');

        $ticketPriority->delete();

        return $this->sendSuccess('Ticket Priority deleted successfully.');
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function activeDeActiveCategory($id)
    {
        $ticketPriority = TicketPriority::findOrFail($id);
        $ticketPriority->status = ! $ticketPriority->status;
        $ticketPriority->save();

        return $this->sendSuccess('Status updated successfully.');
    }
}
