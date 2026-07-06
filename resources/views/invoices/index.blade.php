@extends('layouts.app')
@section('title')
    {{ __('messages.invoices') }}
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ mix('assets/css/invoices/invoices.css') }}">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css">
@endsection

@section('css')
    @livewireStyles
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoices') }}</h1>

            <div class="section-header-breadcrumb float-right">

                {{-- ✅ Month Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        {{ Form::text('month', null, [
                            'id' => 'filterMonth',
                            'class' => 'form-control month-picker',
                            'placeholder' => __('messages.placeholder.select_month'),
                            'autocomplete' => 'off',
                            'readonly' => true,
                        ]) }}
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-month" title="Clear Month Filter">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ✅ Start Date Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        <input type="text" id="filterStartDate" class="form-control"
                            placeholder="Start Date" autocomplete="off" readonly
                            style="min-width: 130px;">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-start-date" title="Clear Start Date">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ✅ End Date Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        <input type="text" id="filterEndDate" class="form-control"
                            placeholder="End Date" autocomplete="off" readonly
                            style="min-width: 130px;" disabled>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-end-date" title="Clear End Date">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ✅ Customer Filter with Clear Button --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        <select name="customer" id="filterCustomer" class="form-control select2-sm"
                            style="min-width: 180px;">
                            <option value="" disabled selected>Select Customer</option>
                            @foreach ($customersData ?? [] as $customer)
                                <option value="{{ $customer['id'] }}">{{ $customer['text'] }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-customer" title="Clear Customer">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>{{-- ✅ Month Filter --}}
                <!-- <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        {{ Form::text('month', null, [
                            'id' => 'filterMonth',
                            'class' => 'form-control month-picker',
                            'placeholder' => __('messages.placeholder.select_month'),
                            'autocomplete' => 'off',
                            'readonly' => true,
                        ]) }}
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-month" title="Clear Month Filter">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div> -->

                {{-- ✅ Start Date Filter --}}
                <!-- <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        <input type="text" id="filterStartDate" class="form-control"
                            placeholder="Start Date" autocomplete="off" readonly
                            style="min-width: 130px;">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-start-date" title="Clear Start Date">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div> -->

                {{-- ✅ End Date Filter --}}
                <!-- <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        <input type="text" id="filterEndDate" class="form-control"
                            placeholder="End Date" autocomplete="off" readonly
                            style="min-width: 130px;" disabled>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-end-date" title="Clear End Date">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div> -->

                {{-- ✅ Customer Filter with Clear Button --}}
                <!-- <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        <select name="customer" id="filterCustomer" class="form-control select2-sm"
                            style="min-width: 180px;">
                            <option value="" disabled selected>Select Customer</option>
                            @foreach ($customersData ?? [] as $customer)
                                <option value="{{ $customer['id'] }}">{{ $customer['text'] }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-customer" title="Clear Customer">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div> -->

                {{-- ✅ Branch Filter --}}
                <!-- <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('branches', $usersBranches ?? [], null, [
                        'id' => 'filterBranch',
                        'class' => 'form-control select2-sm',
                        'placeholder' => __('messages.placeholder.branches'),
                        'style' => 'min-width: 150px;',
                    ]) }}
                </div> -->

                {{-- ✅ Payment Status Filter --}}
                <!-- <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('payment_status', $paymentStatuses, null, [
                        'id' => 'paymentStatus',
                        'class' => 'form-control select2-sm',
                        'placeholder' => __('messages.placeholder.select_status'),
                        'style' => 'min-width: 150px;',
                    ]) }}
                </div> -->

                {{-- ✅ Branch Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('branches', $usersBranches ?? [], null, [
                        'id' => 'filterBranch',
                        'class' => 'form-control select2-sm',
                        'placeholder' => __('messages.placeholder.branches'),
                        'style' => 'min-width: 150px;',
                    ]) }}
                </div>

                {{-- ✅ Payment Status Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('payment_status', $paymentStatuses, null, [
                        'id' => 'paymentStatus',
                        'class' => 'form-control select2-sm',
                        'placeholder' => __('messages.placeholder.select_status'),
                        'style' => 'min-width: 150px;',
                    ]) }}
                </div>
            </div>

            <div class="float-right">
                @can('create_invoices')
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.common.add') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('invoices')
                </div>
            </div>
        </div>
    </section>

    @include('invoices.templates.templates')
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')

    <script>
        $(document).ready(function() {
            // ✅ Month picker
            $('#filterMonth').datepicker({
                format: "MM yyyy",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                endDate: new Date(),
                templates: {
                    leftArrow: '<i class="fas fa-chevron-left"></i>',
                    rightArrow: '<i class="fas fa-chevron-right"></i>'
                }
            }).on('changeDate', function(e) {
                if (e.date) {
                    const date = e.date;
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    Livewire.emit('filterMonth', `${year}-${month}`);
                }
            });

            $('.clear-month').on('click', function() {
                $('#filterMonth').val('').datepicker('update');
                Livewire.emit('filterMonth', '');
            });

            // ✅ Customer select2
            $('#filterCustomer').select2({
                width: '180px',
                dropdownAutoWidth: true,
                placeholder: "Select Customer",
                allowClear: false // we are using custom clear button
            }).on('change', function() {
                Livewire.emit('filterCustomer', $(this).val());
            });

            // ✅ Clear Customer Filter
            $('.clear-customer').on('click', function() {
                $('#filterCustomer').val(null).trigger('change');
                Livewire.emit('filterCustomer', '');
            });

            // ✅ Branch Filter
            $('#filterBranch').select2({
                width: '150px',
                dropdownAutoWidth: true,
                placeholder: "Select Branch",
                allowClear: true
            }).on('change', function() {
                Livewire.emit('filterBranch', $(this).val());
            });

            // ✅ Payment Status Filter
            $('#paymentStatus').select2({
                width: '150px',
                dropdownAutoWidth: true,
                placeholder: "Select Status",
                allowClear: true
            }).on('change', function() {
                Livewire.emit('filterStatus', $(this).val());
            });

            // ✅ Start Date Filter
            $('#filterStartDate').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true,
                templates: {
                    leftArrow: '<i class="fas fa-chevron-left"></i>',
                    rightArrow: '<i class="fas fa-chevron-right"></i>'
                }
            }).on('changeDate', function(e) {
                if (e.date) {
                    const startDate = $(this).val();
                    // Enable end date and set its start date constraint
                    $('#filterEndDate').prop('disabled', false);
                    $('#filterEndDate').datepicker('setStartDate', e.date);
                    // If end date is already set and is before new start date, clear it
                    const endDateVal = $('#filterEndDate').val();
                    if (endDateVal && endDateVal < startDate) {
                        $('#filterEndDate').val('').datepicker('update');
                    }
                    // Emit only if both dates are set
                    const endDate = $('#filterEndDate').val();
                    if (endDate) {
                        // Clear month filter when date range is active
                        $('#filterMonth').val('').datepicker('update');
                        Livewire.emit('filterMonth', '');
                        Livewire.emit('filterDateRange', startDate, endDate);
                    }
                }
            });

            // ✅ Clear Start Date
            $('.clear-start-date').on('click', function() {
                $('#filterStartDate').val('').datepicker('update');
                $('#filterEndDate').val('').datepicker('update').prop('disabled', true);
                $('#filterEndDate').datepicker('setStartDate', false);
                Livewire.emit('filterDateRange', '', '');
            });

            // ✅ End Date Filter
            $('#filterEndDate').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true,
                templates: {
                    leftArrow: '<i class="fas fa-chevron-left"></i>',
                    rightArrow: '<i class="fas fa-chevron-right"></i>'
                }
            }).on('changeDate', function(e) {
                if (e.date) {
                    const startDate = $('#filterStartDate').val();
                    const endDate = $(this).val();
                    if (!startDate) {
                        alert('Please select a Start Date first.');
                        $(this).val('').datepicker('update');
                        return;
                    }
                    // Clear month filter when date range is active
                    $('#filterMonth').val('').datepicker('update');
                    Livewire.emit('filterMonth', '');
                    Livewire.emit('filterDateRange', startDate, endDate);
                }
            });

            // ✅ Clear End Date
            $('.clear-end-date').on('click', function() {
                $('#filterEndDate').val('').datepicker('update');
                const startDate = $('#filterStartDate').val();
                if (startDate) {
                    Livewire.emit('filterDateRange', startDate, '');
                }
            });
        });
    </script>

    <script src="{{ url('assets/js/invoices/invoices-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
@endsection
