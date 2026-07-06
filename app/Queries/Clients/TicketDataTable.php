<?php

namespace App\Queries\Clients;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TaskDataTable
 */
class TicketDataTable
{
    public function get(): Builder
    {
        $contactID = getLoggedInUser()->contact->id;
        $query = Ticket::with(['user', 'department', 'ticketPriority', 'ticketStatus'])
            ->where('contact_id', $contactID)->select('tickets.*');

        return $query;
    }
}
