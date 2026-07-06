@extends('clients.layouts.app')
@section('title')
    {{ __('messages.ticket.ticket_details') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.ticket.ticket_details') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('client.tickets.index') }}"
                   class="btn btn-primary form-btn float-right-mobile">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('clients.tickets.show_fields')
        </div>
    </section>
    @include('ticket_replies.edit_reply_modal')
@endsection
@section('page_scripts')
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/ticket-reply.js') }}"></script>
@endsection
