@extends('layouts.app')
@section('title')
    {{ __('messages.settings') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 19px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 4px;
            right: 4px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.settings') }}</h1>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
            <div class="card">
                <div class="card-body">
                    @include('settings.setting_menu')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    @if ($groupName !== 'note')
        <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    @endif
@endsection
@section('scripts')
    <script>
        let utilsScript = "{{ asset('assets/js/int-tel/js/utils.min.js') }}";
        let isEdit = true;
        let phoneNo = "{{ old('prefix_code') . old('phone') }}";
        let groupName = "{{ $groupName }}";
    </script>
    @if ($groupName !== 'general' && $groupName !== 'note')
        <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>
    @endif
    <script src="{{ mix('assets/js/settings/setting.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#timezone').select2({
                width: '100%',
            });
            $('#currencySelect').select2({
                width: '100%',
            });
            $('#date_formatSelect').select2({
                width: '100%',
            });


            $('#country_select').select2({
                width: '100%',
            });
            $('#state_select').select2({
                width: '100%',
            });


        });
    </script>
    <script>
        var states =
            @json($states); // Assuming $states is a collection of states with 'country_id' and 'name'

        function populateStates(countryId, selectedStateId = null) {
            var stateSelect = $('#state_select'); // The state dropdown

            // Clear the current state options
            stateSelect.empty();

            // Add a default placeholder
            stateSelect.append('<option value="">{{ __('messages.cities.select_state') }}</option>');

            // Filter and append the states that match the selected country
            $.each(states, function(key, state) {
                if (state.country_id == countryId) {
                    stateSelect.append('<option value="' + state.id + '">' + state.name + '</option>');
                }
            });

            // Set the selected state if we are in edit mode
            if (selectedStateId) {
                stateSelect.val(selectedStateId);
            }
        }

        $(document).ready(function() {
            var countrySelect = $('#country_select');
            var initialCountry = countrySelect.val(); // Get the initially selected country
            var selectedState = '{{ $settings['state'] ?? null }}'; // Get the selected state in edit mode

            // If a country is already selected (in edit mode), populate the states
            if (initialCountry) {
                populateStates(initialCountry, selectedState);
            }

            // When the country is changed, dynamically load the states
            countrySelect.on('change', function() {
                var countryId = $(this).val(); // Get the selected country ID
                populateStates(countryId);
            });
        });
    </script>
    <script></script>
@endsection
