@extends('layouts.app')
@section('title')
    {{ __('messages.appointments.edit') }}
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
            <h1>{{ __('messages.appointments.edit') }}</h1>
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
                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $appointment->id, ['id' => 'appointment_id']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('name', __('messages.appointments.name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', $appointment->name ?? null, ['class' => 'form-control', 'required', 'id' => 'name', 'autocomplete' => 'off']) }}
                                </div>
                                
                                
                                <div class="form-group col-md-4">
                                    {{ Form::label('mobile', __('messages.appointments.mobile').(':')) }} <span class="required">*</span><br>
                                    {{ Form::tel('mobile', $appointment->mobile ?? null, ['class' => 'form-control', 'required', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                                    {{ Form::hidden('prefix_code',old('prefix_code'),['id'=>'prefix_code']) }}
                                    <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                                    <span id="error-msg" class="hide"></span>
                                </div>
                                
                                <div class="form-group col-sm-4">
                                    {{ Form::label('email', __('messages.appointments.email') . ':') }} 
                                    {{ Form::email('email', $appointment->email ?? null, ['class' => 'form-control',  'id' => 'email', 'autocomplete' => 'off']) }}
                                </div>
                                
                                <div class="form-group col-sm-4">
                                    {{ Form::label('appointment_date', __('messages.appointments.appointment_date') . ':') }}<span class="required">*</span>
                                    {{ Form::date('appointment_date', $appointment->appointment_date ?? null, ['class' => 'form-control', 'required', 'id' => 'appointment_date', 'autocomplete' => 'off']) }}
                                </div>
                                
                                <div class="form-group col-sm-4">
                                    {{ Form::label('appointment_time', __('messages.appointments.appointment_time') . ':') }}<span class="required">*</span>
                                    {{ Form::time('appointment_time', $appointment->appointment_time ?? null, ['class' => 'form-control', 'required', 'id' => 'appointment_time', 'autocomplete' => 'off']) }}
                                </div>
                                 
                                <div class="form-group col-md-6">
                                    {{ Form::label('appointed_by', __('messages.appointments.appointed_by') . ':') }}<span class="required">*</span>
                                    <div class="input-group">
                                        <input type="text" id="appointed_by" name="appointed_by" class="form-control" value="<?= $appointment->appointed_by ?? null ?>" required style="width: 50%"  > 
                                        <div class="input-group-append" style="width: 50%" >
                                            {{ Form::select('employee_id', ['' => 'Select Employee'] + ($employee_list ?? collect())->toArray(), null, ['class' => 'form-control select2', 'id' => 'employee_id']) }}

                                        </div>
                                    </div>
                                </div>

                            <div class="col-md-12 text-right">
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
    </script>
    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';


        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnSave', 'loading');
            let id = $('#appointment_id').val();

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
                url: route('appointments.update', id),
                type: 'put',
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
                    processingBtn('#editForm', '#btnSave');
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
