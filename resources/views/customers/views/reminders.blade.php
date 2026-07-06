@extends('customers.show')
@section('section')
    <section class="section">
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-header">
                    <div class="mt-0 mb-3 col-12 d-flex justify-content-end livewire-search">
                        <div class="text-right">
                            <a href="javascript:void(0)"
                               class="btn btn-primary addReminderModal add-button text-nowrap">{{ __('messages.reminder.set_reminder') }}
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('reminders.table')
                </div>
            </div>
        </div>
        @include('reminders.add_modal')
        @include('reminders.edit_modal')
        @include('reminders.templates.templates')
    </section>
@endsection
@push('page-scripts')
    <script> 
        let reminderTo= "{{ __('messages.reminder.reminder_to')}}"
    </script>
    <script src="{{mix('assets/js/reminder/reminder.js')}}"></script>
@endpush
