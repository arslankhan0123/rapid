<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\PredefinedReply;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Repositories\TicketRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\MediaStream;

class TicketController extends AppBaseController
{
    /** @var TicketRepository */
    private $ticketRepository;

    public function __construct(TicketRepository $ticketRepo)
    {
        $this->ticketRepository = $ticketRepo;
    }

    /**
     * Display a listing of the Ticket.
     *
     * @return Factory|View
     */
    public function index()
    {
        $statusArr = TicketStatus::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $ticketPriorityArr = TicketPriority::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('tickets.index', compact('statusArr', 'ticketPriorityArr'));
    }

    /**
     * Show the form for creating a new Ticket.
     *
     * @return Factory|View
     */
    public function create()
    {
        $data = $this->ticketRepository->getSyncList();

        return view('tickets.create', compact('data'));
    }

    /**
     * Store a newly created Ticket in storage.
     *
     * @param  CreateTicketRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateTicketRequest $request)
    {
        $input = $request->all();
        $input['ticket_status_id'] = TicketStatus::where('id', 1)->value('id');
        $this->ticketRepository->create($input);

        Flash::success(__('messages.ticket.ticket_saved_successfully'));

        return redirect(route('ticket.index'));
    }

    /**
     * Display the specified Ticket.
     *
     * @param  Ticket  $ticket
     * @return Factory|View
     */
    public function show(Ticket $ticket)
    {
        $data = $this->ticketRepository->getReminderData($ticket->id, Ticket::class);
        $status = Task::STATUS;
        $priorities = Task::PRIORITY;
        $notes = $this->ticketRepository->getNotesData($ticket);
        $groupName = (request('group') == null) ? 'ticket_details' : (request('group'));
        $ticket->load([
            'department', 'ticketPriority', 'ticketStatus', 'service', 'ticketReplies',
        ]);

        return view("tickets.views.$groupName",
            compact('ticket', 'data', 'status', 'priorities', 'notes', 'groupName'));
    }

    /**
     * Show the form for editing the specified Ticket.
     *
     * @param  Ticket  $ticket
     * @return Factory|View
     */
    public function edit(Ticket $ticket)
    {
        $data = $this->ticketRepository->getSyncList();

        return view('tickets.edit', compact('ticket', 'data'));
    }

    /**
     * Update the specified Ticket in storage.
     *
     * @param  Ticket  $ticket
     * @param  UpdateTicketRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function update(Ticket $ticket, UpdateTicketRequest $request)
    {
        $input = $request->all();
        $this->ticketRepository->update($input, $ticket);

        Flash::success(__('messages.ticket.ticket_updated_successfully'));

        return redirect(route('ticket.index'));
    }

    /**
     * Remove the specified Ticket from storage.
     *
     * @param  Ticket  $ticket
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        activity()->performedOn($ticket)->causedBy(getLoggedInUser())
            ->useLog('Ticket deleted.')->log($ticket->subject.' Ticket deleted.');

        $ticket->tags()->delete();
        $ticket->clearMediaCollection(Ticket::TICKET_ATTACHMENT_PATH);
        $ticket->delete();

        return $this->sendSuccess('Ticket deleted successfully.');
    }

    /**
     * @param  int  $predefinedReplyId
     * @return mixed
     */
    public function getPredefinedReplyBody($predefinedReplyId)
    {
        return PredefinedReply::whereId($predefinedReplyId)->value('body');
    }

    /**
     * @param  Ticket  $ticket
     * @return MediaStream
     */
    public function downloadMedia(Ticket $ticket): MediaStream
    {
        $downloads = $ticket->getMedia(Ticket::TICKET_ATTACHMENT_PATH);

        return MediaStream::create('attachments.zip')->addMedia($downloads);
    }

    /**
     * @param  Ticket  $ticket
     * @return mixed
     */
    public function getNotesCount(Ticket $ticket)
    {
        return $this->sendResponse($ticket->notes()->count(), 'Notes count retrieved successfully.');
    }

    /**
     * @return Factory|View
     */
    public function kanbanList()
    {
        $tickets = Ticket::with([
            'user.media', 'ticketPriority', 'service', 'department',
        ])->get()->groupBy('ticket_status_id');

        $ticketStatus = TicketStatus::get();

        return View('tickets.kanban.index', compact('tickets', 'ticketStatus'));
    }

    /**
     * @param  Ticket  $ticket
     * @param  int  $statusId
     * @return mixed
     */
    public function changeStatus(Ticket $ticket, $statusId)
    {
        $ticket->update(['ticket_status_id' => $statusId]);

        return $this->sendSuccess(__('messages.ticket.ticket_status_updated_successfully'));
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function attachmentDelete(Request $request)
    {
        $mediaId = $request->all();
        $attachment = Media::findOrFail($mediaId['mediaId'])->delete();

        return $this->sendSuccess('Attachment deleted successfully.');
    }

    /**
     * @param  Media  $mediaItem
     * @return Media
     */
    public function download(Media $mediaItem): Media
    {
        return $mediaItem;
    }
}
