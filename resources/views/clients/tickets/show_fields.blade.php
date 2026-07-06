<div class="row">
    <div class="col-lg-4 col-md-12">
        <h5 class="text-dark">{{ __('messages.ticket.ticket_information') }}</h5>
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <h6 class="text-dark">#{{ $ticket->id }} - {{ html_entity_decode($ticket->subject) }}</h6>
                </div>
                <div class="form-group">
                    {{ Form::label('status', __('messages.common.status').':') }}
                    <p><span class="badge badge-primary"
                             style="background-color: {{ $ticket->ticketStatus->pick_color }};">{{ $ticket->ticketStatus->name }}</span>
                    </p>
                </div>
                <div class="form-group">
                    {{ Form::label('priority', __('messages.ticket.priority').':') }}
                    <p>{{ !empty($ticket->ticketPriority->name) ? html_entity_decode($ticket->ticketPriority->name) : __('messages.common.n/a')}}</p>
                </div>
                <div class="form-group">
                    {{ Form::label('created_at', 'Submitted :') }}
                    <p>{{ Carbon\Carbon::parse($ticket->created_at)->translatedFormat('jS M, Y H:i A') }}</p>
                </div>
                <div class="form-group">
                    {{ Form::label('body', 'Description :') }}
                    <p>{!! $ticket->body ?? 'N/A' !!}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-md-12">
        @include('ticket_replies.add_reply')
        <div class="mt-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-dark">{{ __('messages.ticket.replies') }}</h3>
                </div>
                <div class="card-body scroll-tickets">
                    @forelse($ticket->ticketReplies as $ticketReply)
                        <div class="card">
                            <div class="card-body custom-ticket-card-body">
                                <div class="row d-flex align-items-center">
                                    <div class="col-sm-12 col-md-1 col-xl-1">
                                        <img src="{{ $ticketReply->image_url }}" alt="profile"
                                             class="ticket-user-avatar mb-md-0 mb-3">
                                    </div>
                                    <div class="col-sm-12 col-md-4 col-xl-4">
                                        <div class="d-flex align-items-center mt-2 ml-xl-2 ml-lg-2 ml-0">
                                            <h6>{{ $ticketReply->full_name }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-7 col-xl-7">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span class="mr-2">{{ $ticketReply->pivot->created_at->diffForHumans() }}</span>
                                            @if(getLoggedInUser()->hasRole('client') && getLoggedInUserId() == $ticketReply->pivot->user_id)
                                                <a title="{{ __('messages.common.edit') }}"
                                                   class="edit-reply-btn edit-ticket-reply"
                                                   data-id="{{ $ticketReply->pivot->id }}" href="javascript:void(0)">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a title="{{ __('messages.common.delete') }}"
                                                   class="delete-reply-btn delete-ticket-reply"
                                                   data-id="{{ $ticketReply->pivot->id }}" href="javascript:void(0)">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <p>{!! $ticketReply->pivot->reply !!}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center">{{ __('messages.ticket.no_ticket_reply_found') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>


