<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateTicketRequest;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Queries\Clients\TicketDataTable;
use App\Repositories\TicketRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Yajra\DataTables\DataTables;

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
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->all()) {
            return DataTables::of((new TicketDataTable())->get())->make(true);
        }

        $statusArr = TicketStatus::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $ticketPriorityArr = TicketPriority::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('clients.tickets.index', compact('statusArr', 'ticketPriorityArr'));
    }

    /**
     * Show the form for creating a new Ticket.
     *
     * @return Factory|View
     */
    public function create()
    {
        $data = $this->ticketRepository->getSyncList();

        return view('clients.tickets.create', compact('data'));
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
        $input['contact_id'] = getLoggedInUser()->contact->id;
        $input['ticket_status_id'] = TicketStatus::where('id', 1)->value('id');
        $ticket = $this->ticketRepository->create($input);

        Flash::success(__('messages.ticket.ticket_saved_successfully'));

        return redirect(route('client.tickets.show', $ticket->id));
    }

    /**
     * Display the specified Ticket.
     *
     * @param  Ticket  $ticket
     * @return Application|Factory|View|RedirectResponse
     */
    public function show(Ticket $ticket)
    {
        $contactId = getLoggedInUser()->contact->id;
        $clientTicketIds = Ticket::whereContactId($contactId)->pluck('id')->toArray();

        if (! in_array($ticket->id, $clientTicketIds)) {
            return redirect()->back();
        }

        $ticket->load([
            'department', 'ticketPriority', 'ticketStatus', 'service', 'ticketReplies',
        ]);

        return view('clients.tickets.show', compact('ticket'));
    }

    /**
     * @param  Ticket  $ticket
     * @return mixed
     */
    public function destroy(Ticket $ticket)
    {
        $contactId = getLoggedInUser()->contact->id;

        $clientTicketIds = Ticket::whereContactId($contactId)->pluck('id')->toArray();

        if (! in_array($ticket->id, $clientTicketIds)) {
            return $this->sendError(__('messages.seems_you_are_not_allowed_to_access_this_record'));
        }

        $ticket->tags()->delete();
        $ticket->clearMediaCollection(Ticket::TICKET_ATTACHMENT_PATH);
        $ticket->delete();

        return $this->sendSuccess('Ticket deleted successfully.');
    }
}
