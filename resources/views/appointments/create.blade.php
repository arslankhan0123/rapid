@extends('layouts.app')
@section('title')
    {{ __('messages.appointments.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.appointments.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('appointments.index') }}" class="btn btn-primary form-btn">List</a>
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
                                <div class="form-group col-sm-8">
                                    {{ Form::label('name', __('messages.appointments.name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'name', 'autocomplete' => 'off']) }}
                                </div>
                                
                                 
                                <div class="form-group col-md-4">
                                    {{ Form::label('mobile', __('messages.appointments.mobile').(':')) }} <span class="required">*</span> <br>
                                    {{ Form::tel('mobile', null, ['class' => 'form-control','id' => 'phoneNumber', 'required', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'placeholder'=>__('messages.appointments.mobile')]) }}
                                    {{ Form::hidden('prefix_code',old('prefix_code'),['id'=>'prefix_code']) }}
                                    <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                                    <span id="error-msg" class="hide"></span>
                                </div>
                                                    
                                <div class="form-group col-sm-4">
                                    {{ Form::label('email', __('messages.appointments.email') . ':') }} 
                                    {{ Form::email('email', null, ['class' => 'form-control',  'id' => 'email', 'autocomplete' => 'off']) }}
                                </div>
                                
                                <div class="form-group col-sm-4">
                                    {{ Form::label('appointment_date', __('messages.appointments.appointment_date') . ':') }}<span class="required">*</span>
                                    {{ Form::date('appointment_date', null, ['class' => 'form-control', 'required', 'id' => 'appointment_date', 'autocomplete' => 'off']) }}
                                </div>
                                
                                <div class="form-group col-sm-4">
                                    {{ Form::label('appointment_time', __('messages.appointments.appointment_time') . ':') }}<span class="required">*</span>
                                    {{ Form::time('appointment_time', null, ['class' => 'form-control', 'required', 'id' => 'appointment_time', 'autocomplete' => 'off']) }}
                                </div>
                                 
                                <div class="form-group col-md-6">
                                    {{ Form::label('appointed_by', __('messages.appointments.appointed_by') . ':') }}<span class="required">*</span>
                                    <div class="input-group">
                                        <input type="text" id="appointed_by" name="appointed_by" class="form-control" required style="width: 50%" > 
                                        <div class="input-group-append" style="width: 50%" >
                                            {{ Form::select('employee_id', ['' => 'Select Employee'] + ($employee_list ?? collect())->toArray(), null, ['class' => 'form-control select2',  'id' => 'employee_id']) }}


                                        </div>
                                    </div>
                                </div>


                            <div class="col-sm-12 mt-5 text-right">
                                
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
    <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
    <script>
        let utilsScript = "{{ asset('assets/js/int-tel/js/utils.min.js') }}"
        let phoneNo = "{{ old('prefix_code') . old('phone') }}" 
        defaultCountryCodeValue = 'sa'; // Set UAE as default
    </script>
    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>
    
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        let departmentNewCreateUrl = route('appointments.store');
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
                        const url = route('appointments.index', );
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
    </script>
    <script>
        $(document).ready(function () {
            $('#employee_id').on('change', function () {
                var selectedText = $('#employee_id option:selected').text();
                $('#appointed_by').val(selectedText);
            });
        });
    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("appointment_date").setAttribute("min", today);
        });
    </script>
 
@endsection
