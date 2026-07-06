@extends('layouts.app')
@section('title')
    {{ __('messages.cities.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.cities.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('cities.index') }}" class="btn btn-primary form-btn">{{ __('messages.cities.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.cities.name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('country_id', __('messages.cities.country') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('country_id', $countries, null, ['class' => 'form-control', 'required', 'id' => 'country_select', 'placeholder' => __('messages.cities.select_country')]) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('state_id', __('messages.cities.state') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('state_id', [], null, ['class' => 'form-control', 'required', 'id' => 'state_select', 'placeholder' => __('messages.cities.select_state')]) }}
                                </div>

                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.cities.description') . ':') }}
                                    {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                                </div>
                            </div>
                            <div class="text-right mr-1">
                                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
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
        let departmentNewCreateUrl = route('cities.store');
        $(document).on('submit', '#addNewFormDepartmentNew', function(event) {
            event.preventDefault();
            processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');

            let description = $('<div />').
            html($('#createDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#createDescription').summernote('isEmpty')) {
                $('#createDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewFormDepartmentNew', '#btnSave', 'reset');
                return false;
            }
            $.ajax({
                url: departmentNewCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('cities.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewFormDepartmentNew', '#btnSave');
                },
            });
        });

        $(document).ready(function() {
            $('#country_select').select2({
                width: '100%',
            });
            $('#state_select').select2({
                width: '100%',
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Group states by country_id for easier access
            var statesByCountry = @json($states->groupBy('country_id'));

            $('#country_select').on('change', function() {
                var selectedCountry = $(this).val();
                var $stateSelect = $('#state_select');

                // Clear the state dropdown
                $stateSelect.empty().append('<option value="">' +
                    "{{ __('messages.cities.select_state') }}" + '</option>');

                // Populate state dropdown based on selected country
                if (statesByCountry[selectedCountry]) {
                    $.each(statesByCountry[selectedCountry], function(index, state) {
                        $stateSelect.append(new Option(state.name, state.id));
                    });
                }
            });

            // Trigger change event on country select if country is already selected
            if ($('#country_select').val()) {
                $('#country_select').trigger('change');
            }
        });
    </script>
@endsection
