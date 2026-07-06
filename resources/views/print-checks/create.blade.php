@extends('layouts.app')
@section('title')
    {{ __('messages.print-checks.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.print-checks.add') }}</h1>
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

                <div class="modal-content">

                    {{ Form::open(['id' => 'addAssetForm', 'enctype' => 'multipart/form-data']) }}
                    <div class="modal-body">

                        <div class="row">

                            <!-- Branch Field -->
                            <div class="form-group col-md-6">
                                {{ Form::label('branch_id', 'Branch') }}<span class="required">*</span>
                                {{ Form::select('branch_id', $usersBranches ?? [], null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('date', 'Date') }}<span class="required">*</span>
                                {{ Form::date('date', null, ['class' => 'form-control', 'required']) }}
                            </div>
                            <!-- Check Number Field -->
                            <div class="form-group col-md-6">
                                {{ Form::label('check_number', 'Check Number') }}<span class="required">*</span>
                                {{ Form::text('check_number', null, ['class' => 'form-control', 'required']) }}
                            </div>

                            <!-- Issue Name Field -->
                            <div class="form-group col-md-6">
                                {{ Form::label('issue_name', 'Issue Name') }}<span class="required">*</span>
                                {{ Form::text('issue_name', null, ['class' => 'form-control', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('issue_name', 'Place Of Issue') }}<span class="required">*</span>
                                {{ Form::text('issue_place', null, ['class' => 'form-control', 'required']) }}
                            </div>
                            <!-- Amount Field -->
                            <div class="form-group col-md-6">
                                {{ Form::label('amount', 'Amount') }}<span class="required">*</span>
                                {{ Form::number('amount', null, ['class' => 'form-control', 'required', 'step' => '0.01']) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('branch_id', 'Bank') }}<span class="required">*</span>
                                {{ Form::select('bank_id', $banks ?? [], null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select bank']) }}
                            </div>
                            <!-- Date Field -->

                        </div>


                        <div class="text-right mr-3">
                            {{ Form::button('Submit & Print', ['type' => 'submit', 'class' => 'btn btnWarning text-white submit-print', 'id' => 'btnSavePrint', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...", 'style' => 'line-height:32px;']) }}

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

    <!-- AJAX script -->
    <script type="text/javascript">
        let assetCreateUrl = route('print-checks.store');
        let assetUrl = route('print-checks.index') + '/';

        $(document).on('submit', '#addAssetForm', function(event) {
            event.preventDefault();
            var isPrint = false;
            var formData = new FormData(this);

            var submitter = event.originalEvent.submitter;

            // Check if the Submit & Print button was clicked
            if ($(submitter).hasClass('submit-print')) {
                formData.append('print', 'true'); // Append 'print' parameter to the form data
                processingBtn('#addAssetForm', '#btnSavePrint', 'loading');
                isPrint = true;
            } else {
                processingBtn('#addAssetForm', '#btnSave', 'loading');
            }

            $.ajax({
                type: 'POST',
                url: assetCreateUrl, // Update with your actual route
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btnSave').attr('disabled', true).html(
                        "<span class='spinner-border spinner-border-sm'></span> Processing..."
                    );
                },
                success: function(response) {
                    if (response.success) {
                        displaySuccessMessage(response.message);
                        $('#addAssetForm')[0].reset();
                        $('#createDescription').val('');
                        $('#createDescription').summernote('code', '');

                        if (isPrint && response.data.id) {
                            const url = route('print-checks.export.print', [response.data.id]);
                            window.open(url, '_blank');
                        } else {
                            const url = route('print-checks.index', );
                            window.location.href = url;

                        }
                    }
                    processingBtn('#addAssetForm', '#btnSave');
                    processingBtn('#addAssetForm', '#btnSavePrint');
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addAssetForm', '#btnSave');
                    processingBtn('#addAssetForm', '#btnSavePrint');
                },
            });
        });
    </script>
@endsection
