@extends('layouts.app')
@section('title')
    {{ __('messages.ticket.ticket_details') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    @include('ticket_replies.edit_reply_modal')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.ticket.ticket_details') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <a href="{{ route('ticket.edit', ['ticket' => $ticket->id]) }}"
                   class="btn btn-warning mr-2 form-btn">{{ __('messages.common.edit') }}
                </a>
                <a href="{{ route('ticket.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('tickets.show_fields')
                </div>
            </div>
            @if($groupName == 'ticket_details')
                <div class="row">
                <div class="col-lg-6 col-sm-12">
                    @include('ticket_replies.add_reply')
                </div>
                <div class="col-lg-6 col-sm-12">
                    <h4 class="text-dark">{{ __('messages.ticket.replies') }}</h4>
                    <div class="card">
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
                                                    <a title="{{ __('messages.common.edit') }}" class="edit-reply-btn edit-ticket-reply" data-id="{{ $ticketReply->pivot->id }}" href="javascript:void(0)">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a title="{{ __('messages.common.delete') }}" class="delete-reply-btn delete-ticket-reply" data-id="{{ $ticketReply->pivot->id }}" href="javascript:void(0)">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
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
            @endif
        </div>
        @include('tasks.templates.templates')
        @include('reminders.templates.templates')
        @include('reminders.add_modal')
        @include('reminders.edit_modal')
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let statusArray = JSON.parse('@json($status)')
        let priorities = JSON.parse('@json($priorities)')
        let ownerId = "{{ $ticket->id }}"
        let ownerType = 'App\\Models\\Ticket'
        let ticketUrl = "{{ route('ticket.index') }}/"
        let ticketId = '{{ $ticket->id }}'
        let authId = '{{ getLoggedInUserId() }}'
        let ownerUrl = "{{ route('ticket.index') }}"
        let memberUrl = "{{ route('members.index') }}"
    </script>
    <script src="{{ mix('assets/js/tasks/tasks.js')}}"></script>
    <script src="{{ mix('assets/js/notes/new-notes.js')}}"></script>
    <script src="{{ mix('assets/js/reminder/reminder.js')}}"></script>
    <script src="{{ mix('assets/js/tickets/ticket-details.js') }}"></script>
    <script src="{{ mix('assets/js/ticket-reply.js') }}"></script>
@endsection
