@extends('layouts.app')
@section('title')
    {{ __('messages.revokes.revoke_termination') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.revokes.revoke_termination') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('revokes.index') }}" class="btn btn-primary form-btn float-right">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'revokes.store', 'id' => 'createRevokeForm']) }}
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {{ Form::label('termination_id', __('messages.terminations.select_termination') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select(
                                'termination_id',
                                $terminations->mapWithKeys(function ($termination) {
                                    return [
                                        $termination->id =>
                                            $termination->employee->name .
                                            ' (' .
                                            $termination->employee->iqama_no .
                                            ') - ' .
                                            \Carbon\Carbon::parse($termination->date)->format('d-m-Y'),
                                    ];
                                }),
                                null,
                                [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'termination_select',
                                    'placeholder' => __('messages.terminations.select_termination'),
                                ],
                            ) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('date', __('messages.common.date') . ':') }}<span class="required">*</span>
                            {{ Form::date('date', \Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control', 'required']) }}
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('reason', __('messages.revokes.reason') . ':') }}
                            {{ Form::textarea('reason', null, ['class' => 'form-control summernote-simple', 'id' => 'reason', 'rows' => 4]) }}
                        </div>
                    </div>
                    <div class="form-group col-sm-12 mb-0 mt-3">
                        <div class="form-check form-switch ">
                            {{ Form::label('status', __('messages.revokes.status') . ':') }}
                            {{ Form::checkbox('status', 1, true, ['class' => 'form-check-input ml-4', 'id' => 'statusSwitch']) }}
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
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#termination_select').select2({
                width: '100%',
                placeholder: '{{ __('messages.terminations.select_termination') }}',
                allowClear: true
            });
        });

        $(document).on('submit', '#createRevokeForm', function(event) {
            event.preventDefault();
            processingBtn('#createRevokeForm', '#btnSave', 'loading');

            $.ajax({
                url: "{{ route('revokes.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        window.location.href = "{{ route('revokes.index') }}";
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#createRevokeForm', '#btnSave');
                },
            });
        });
    </script>
@endsection
