@extends('layouts.app')
@section('title')
    {{ __('messages.revokes.edit_revoke') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        #statusSwitchEdit.form-check-input {
            width: 3em;
            height: 1.5em;
        }

        #statusSwitchEdit.form-check-input:checked {
            background-color: #0d6efd;
        }

        #statusSwitchEdit.form-check-input::before {
            width: 1.5em;
            height: 1.5em;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.revokes.edit_revoke') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('revokes.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'editRevokeForm']) }}
                    {{ Form::hidden('id', $revoke->id, ['id' => 'revoke_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {{ Form::label('date', __('messages.common.date') . ':') }}<span class="required">*</span>
                            {{ Form::date('date', $revoke->date ?? null, ['class' => 'form-control', 'required']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('employee', __('messages.revokes.employee') . ':') }}
                            {{ Form::text('employee_name', $revoke->employee->name . ' (' . $revoke->employee->iqama_no . ')', ['class' => 'form-control', 'readonly']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('termination', __('messages.revokes.termination_date') . ':') }}
                            {{ Form::text('termination_date', $revoke->termination->date ? \Carbon\Carbon::parse($revoke->termination->date)->format('d-m-Y') : '', ['class' => 'form-control', 'readonly']) }}
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('reason', __('messages.revokes.reason') . ':') }}
                            {{ Form::textarea('reason', $revoke->reason, ['class' => 'form-control summernote-simple', 'id' => 'editReason', 'rows' => 4]) }}
                        </div>
                    </div>
                    <div class="form-group col-sm-12 mb-0 mt-3">
                        <div class="form-check form-switch ">
                            {{ Form::label('status', __('messages.revokes.status') . ':') }}
                            {{ Form::checkbox('status', 1, $revoke->status, ['class' => 'form-check-input ml-4', 'id' => 'statusSwitchEdit']) }}
                        </div>
                    </div>
                    <div class="text-right mt-3">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script>
        'use strict';
        $(document).on('submit', '#editRevokeForm', function(event) {
            event.preventDefault();
            processingBtn('#editRevokeForm', '#btnSave', 'loading');
            let id = $('#revoke_id').val();

            $.ajax({
                url: route('revokes.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('revokes.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editRevokeForm', '#btnSave');
                },
            });
        });
    </script>
@endsection
