@extends('tickets.show')
@section('section')
    <section class="section">
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-header">
                    <div class="row w-100 justify-content-end">
                        <div>
                            <a href="#" class="btn btn-primary addReminderModal add-button custom-btn-line-height"
                               data-toggle="modal"
                               data-target="#addModal">{{ __('messages.reminder.set_reminder') }} <i
                                        class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('reminders.table')
                </div>
            </div>
        </div>
        @include('reminders.templates.templates')
    </section>
@endsection
