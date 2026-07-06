<?php

namespace App\Http\Controllers;

use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketReplyController extends AppBaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'reply' => 'required',
        ]);

        $input = $request->all();
        $ticketID = $input['ticket_id'];
        $ticketReply = TicketReply::create($input);

        return $this->sendResponse(ticketReplyRedirectUrl($ticketID), __('messages.ticket_reply.ticket_reply_successfully'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $ticketReply = TicketReply::find($id);

        return $this->sendResponse($ticketReply, 'Ticket reply retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required',
        ]);

        $input = $request->all();
        $ticketReply = TicketReply::find($id);
        $ticketReply->update($input);
        $ticketID = $ticketReply->ticket_id;

        return $this->sendResponse(ticketReplyRedirectUrl($ticketID), __('messages.ticket_reply.ticket_reply_updated_successfully'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        if (getLoggedInUser()->hasRole('client')) {
            $clientTicketReplyIds = TicketReply::whereUserId(getLoggedInUserId())->pluck('id')->toArray();

            if (! in_array($id, $clientTicketReplyIds)) {
                return $this->sendError(__('messages.seems_you_are_not_allowed_to_access_this_record'));
            }
        }

        $ticketReply = TicketReply::find($id);
        $ticketID = $ticketReply->ticket_id;
        $ticketReply->delete();

        return $this->sendResponse(ticketReplyRedirectUrl($ticketID), __('messages.ticket_reply.ticket_reply_deleted_successfully'));
    }
}
