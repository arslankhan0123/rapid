@extends('layouts.app')
@section('title')
    {{ __('messages.areas.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.areas.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('areas.index') }}" class="btn btn-primary form-btn">{{ __('messages.areas.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $area->id, ['id' => 'area_id']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.areas.name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', $area->name, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('country_id', __('messages.areas.country') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('country_id', $countries, $area->country_id, ['class' => 'form-control', 'required', 'id' => 'country_select', 'placeholder' => __('messages.states.select_country')]) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('state_id', __('messages.areas.state') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('state_id', [], $area->state_id, ['class' => 'form-control', 'required', 'id' => 'state_select', 'placeholder' => __('messages.areas.select_state')]) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('city_id', __('messages.areas.city') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('city_id', [], $area->city_id, ['class' => 'form-control', 'required', 'id' => 'city_select', 'placeholder' => __('messages.states.select_city')]) }}
                                </div>


                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.assets.category_description') . ':') }}
                                    {{ Form::textarea('description', $area->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
        'use strict';


        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnSave', 'loading');
            let id = $('#area_id').val();

            let description = $('<div />').
            html($('#editCategoryDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#editCategoryDescription').summernote('isEmpty')) {
                $('#editCategoryDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewForm', '#btnSave', 'reset');
                return false;
            }

            $.ajax({
                url: route('areas.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('areas.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editForm', '#btnSave');
                },
            });
        });

        $(document).ready(function() {
            $('#country_select').select2({
                width: '100%',
            });
            $('#city_select').select2({
                width: '100%',
            });
            $('#state_select').select2({
                width: '100%',
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            // Group states and cities by country_id and state_id for easy access
            var statesByCountry = @json($states->groupBy('country_id'));
            var citiesByState = @json($cities->groupBy('state_id'));

            // Function to populate the state dropdown based on selected country
            function populateStates() {
                var selectedCountry = $('#country_select').val();
                var $stateSelect = $('#state_select');
                var selectedState = '{{ old('state_id', $area->state_id ?? '') }}';

                // Clear the state dropdown
                $stateSelect.empty().append('<option value="">' + "{{ __('messages.areas.select_state') }}" +
                    '</option>');

                // Populate state dropdown based on selected country
                if (statesByCountry[selectedCountry]) {
                    $.each(statesByCountry[selectedCountry], function(index, state) {
                        var isSelected = state.id == selectedState ? 'selected' : '';
                        $stateSelect.append(new Option(state.name, state.id, false, isSelected));
                    });
                }
            }

            // Function to populate the city dropdown based on selected state
            function populateCities() {
                var selectedState = $('#state_select').val();
                var $citySelect = $('#city_select');
                var selectedCity = '{{ old('city_id', $area->city_id ?? '') }}';

                // Clear the city dropdown
                $citySelect.empty().append('<option value="">' + "{{ __('messages.states.select_city') }}" +
                    '</option>');

                // Populate city dropdown based on selected state
                if (citiesByState[selectedState]) {
                    $.each(citiesByState[selectedState], function(index, city) {
                        var isSelected = city.id == selectedCity ? 'selected' : '';
                        $citySelect.append(new Option(city.name, city.id, false, isSelected));
                    });
                }
            }

            // Event listener for country dropdown change
            $('#country_select').on('change', function() {
                populateStates();
                // Trigger change event on state dropdown to populate cities
                $('#state_select').trigger('change');
            });

            // Event listener for state dropdown change
            $('#state_select').on('change', function() {
                populateCities();
            });

            // Trigger change event on country select if country is already selected
            if ($('#country_select').val()) {
                $('#country_select').trigger('change');
            }
        });
    </script>
@endsection
