@extends('tasks.show')
@section('section')
    <section class="section">
        <div class="section-body">
            @include('flash::message')
            <div class="row w-100 justify-content-end">
                <a href="#" class="btn btn-primary addReminderModal add-button custom-btn-line-height"
                   data-toggle="modal"
                   data-target="#addModal">{{ __('messages.reminder.set_reminder') }} </a>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('reminders.table')
                </div>
            </div>
        </div>
    </section>
@endsection
