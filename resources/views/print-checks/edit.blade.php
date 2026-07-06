@extends('layouts.app')
@section('title')
    {{ __('messages.print-checks.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.print-checks.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('print-checks.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    {{ Form::open(['id' => 'editFormNew', 'enctype' => 'multipart/form-data']) }}
                    {{ Form::hidden('id', $check->id, ['id' => 'asset_id']) }}


                    <div class="row">
                        <div class="form-group  col-md-6">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], $check->branch_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', 'Date') }}<span class="required">*</span>
                            {{ Form::date('date', $check->date, ['class' => 'form-control', 'required']) }}
                        </div>
                        <!-- Check Number Field -->
                        <div class="form-group col-md-6">
                            {{ Form::label('check_number', 'Check Number') }}<span class="required">*</span>
                            {{ Form::text('check_number', $check->check_number, ['class' => 'form-control', 'required']) }}
                        </div>

                        <!-- Issue Name Field -->
                        <div class="form-group col-md-6">
                            {{ Form::label('issue_name', 'Issue Name') }}<span class="required">*</span>
                            {{ Form::text('issue_name', $check->issue_name, ['class' => 'form-control', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('issue_name', 'Place Of Issue') }}<span class="required">*</span>
                            {{ Form::text('issue_place',  $check->issue_place, ['class' => 'form-control', 'required']) }}
                        </div>

                        <!-- Amount Field -->
                        <div class="form-group col-md-6">
                            {{ Form::label('amount', 'Amount') }}<span class="required">*</span>
                            {{ Form::number('amount', $check->amount, ['class' => 'form-control', 'required', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('branch_id', 'Bank') }}<span class="required">*</span>
                            {{ Form::select('bank_id', $banks ?? [], $check->bank_id ?? null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select bank']) }}
                        </div>
                    </div>

                    <div class="text-right mr-1">
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
        $(document).on('submit', '#editFormNew', function(e) {
            e.preventDefault();
            processingBtn('#editFormNew', '#btnSave', 'loading');
            let id = $('#asset_id').val();
            var formData = new FormData(this);
            $.ajax({
                type: 'post',
                url: route('print-checks.update', id),
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('print-checks.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editFormNew', '#btnSave');
                },
            });
        });
    </script>
@endsection
