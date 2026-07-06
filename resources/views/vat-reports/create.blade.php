@extends('layouts.app')
@section('title')
    {{ __('messages.vat-reports.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.vat-reports.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('vat-reports.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'addNewForm']) }}
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('period', 'Period') }}
                                {{ Form::select('period', ['q1' => 'Q1', 'q2' => 'Q2', 'q3' => 'Q3', 'q4' => 'Q4'], old('period'), ['class' => 'form-control select2','id'=>"period_select", 'placeholder' => 'Select a period']) }}
                            </div>
                        </div>

                        <!-- Input (number) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('input', 'Input') }}
                                {{ Form::number('input', old('input'), ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        </div>

                        <!-- Output (number) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('output', 'Output') }}
                                {{ Form::number('output', old('output'), ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        </div>

                        <!-- Net (number) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('net', 'Net') }}
                                {{ Form::number('net', old('net'), ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        </div>

                        <!-- Paid (number) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('paid', 'Paid') }}
                                {{ Form::number('paid', old('paid'), ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        </div>

                        <!-- Unpaid (number) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('unpaid', 'Unpaid') }}
                                {{ Form::number('unpaid', old('unpaid'), ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    </div>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        let categoryUrl = route('vat-reports.store');
        $(document).on('submit', '#addNewForm', function(event) {
            event.preventDefault();
            processingBtn('#addNewForm', '#btnSave', 'loading');

            $.ajax({
                url: categoryUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);

                        const url = route('vat-reports.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewForm', '#btnSave');
                },
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#period_select').change(function() {
                var period = $(this).val(); // Get selected period value
                var url = "{{ route('vat-reports.input', ':period') }}".replace(':period', period);

                if (period) {
                    // Make AJAX request to fetch data based on selected period
                    $.ajax({
                        url: url, // URL from the route
                        type: 'GET',
                        success: function(response) {
                            console.log(response);
                        },
                        error: function(xhr, status, error) {

                        }
                    });
                }
            });
        });
    </script>
@endsection
