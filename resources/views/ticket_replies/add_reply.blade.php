<h4 class="text-dark">{{ __('messages.ticket.add_reply') }}</h4>
<div class="card">
    {{ Form::open(['id' => 'ticketReplyStoreForm']) }}
    <div class="card-body">
        {{ Form::hidden('ticket_id', $ticket->id) }}
        {{ Form::hidden('user_id', getLoggedInUserId()) }}
        <div class="form-group">
            {{ Form::textarea('reply', null, ['class' => 'form-control', 'id' => 'replyId']) }}
        </div>
        <div class="d-flex justify-content-end">
            {{ Form::submit(__('messages.ticket.reply'),['class' => 'btn btn-primary', 'id' => 'btnReply']) }}
        </div>
    </div>
    {{ Form::close() }}
</div>
