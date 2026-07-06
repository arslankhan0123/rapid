<?php

namespace App\Http\Livewire;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Repositories\TicketRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Tickets extends SearchableComponent
{
    public $searchTickets = '';

    public $filterTicketByStatus = '';

    public $ticketPriorityFilter = '';

    public $customer;

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
        'deleteTicket',
        'filterTicketByStatus',
        'filterTicketPriority',
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Ticket::class;
    }

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return [
            'subject',
        ];
    }

    /**
     * @return Application|Factory
     */
    public function render()
    {
        $tickets = $this->searchTickets();
        $ticketRepo = app(TicketRepository::class);
        $statusCounts = $ticketRepo->getTicketStatusCounts();
        $customer = $this->customer;
        $ticketStatusArr = [];
        if (! empty($customer)) {
            $ticketStatusArr = TicketStatus::pluck('name', 'id');
        }

        return view('livewire.tickets', compact('tickets', 'statusCounts', 'customer', 'ticketStatusArr'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchTickets()
    {
        $this->setQuery($this->getQuery()->with('contact', 'user.media', 'ticketStatus', 'department', 'ticketPriority',
            'service', 'media'));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->filterTicketByStatus !== '', function (Builder $q) {
            $q->where('ticket_status_id', $this->filterTicketByStatus);
        });

        $this->getQuery()->when($this->ticketPriorityFilter !== '', function (Builder $query) {
            $query->where('priority_id', $this->ticketPriorityFilter);
        });

        $this->getQuery()->when($this->customer != null, function (Builder $query) {
            $query->whereHas('contact', function (Builder $q) {
                $q->where('customer_id', $this->customer);
            });
        });

        return $this->paginate();
    }

    /**
     * @return Builder
     */
    public function filterResults()
    {
        $searchableFields = $this->searchableFields();
        $search = $this->search;

        $this->getQuery()->when(! empty($search), function (Builder $q) use ($search, $searchableFields) {
            $this->getQuery()->where(function (Builder $q) use ($search, $searchableFields) {
                $searchString = '%'.$search.'%';
                foreach ($searchableFields as $field) {
                    if (Str::contains($field, '.')) {
                        $field = explode('.', $field);
                        $q->orWhereHas($field[0], function (Builder $query) use ($field, $searchString) {
                            $query->whereRaw("lower($field[1]) like ?", $searchString);
                        });
                    } else {
                        $q->orWhereRaw("lower($field) like ?", $searchString);
                    }
                }
            });
        });

        return $this->getQuery();
    }

    /**
     * @param $status
     */
    public function filterTicketByStatus($status)
    {
        $this->filterTicketByStatus = $status;
        $this->resetPage();
    }

    /**
     * @param $id
     */
    public function filterTicketPriority($id)
    {
        $this->ticketPriorityFilter = $id;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
