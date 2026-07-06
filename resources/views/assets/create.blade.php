@extends('layouts.app')
@section('title')
    {{ __('messages.assets.add_assets') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.assets.add_assets') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('assets.index') }}" class="btn btn-primary form-btn">
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
                            <div class="form-group  col-md-6">
                                {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                                {{ Form::select('branch_id', $usersBranches ?? [], null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                            </div>
                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('name', __('messages.assets.asset_name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('title', __('messages.assets.asset_company_code') . ':') }}
                                    {{ Form::text('company_asset_code', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('category_id', __('messages.assets.category') . ':') }} <span
                                        class="required">*</span>
                                    {{ Form::select('asset_category_id', $categories, null, ['class' => 'form-control', 'id' => 'select_category', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.select_category')]) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('is_working', __('messages.assets.is_working') . ':') }}
                                    {{ Form::select('is_working', ['yes' => 'Yes', 'no' => 'No'], null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('company_name', __('messages.assets.company') . ':') }}
                                    {{ Form::select('company_name', [$companySetting->value => $companySetting->value], $companySetting->value, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('employee_id', __('messages.assets.asset_employee') . ':') }}
                                    {{ Form::select('employee_id', $employees, null, ['class' => 'form-control', 'id' => 'employee_select', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.select_employee')]) }}
                                </div>

                            </div>
                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('employee_id', 'Supplier' . ':') }}
                                    {{ Form::select('supplier_id', $suppliers ?? [], null, ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Select Supplier']) }}
                                </div>

                            </div>

                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('purchase_date', __('messages.assets.asset_purchase_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::date('purchase_date', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('warranty_end_date', __('messages.assets.asset_warranty_end_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::date('warranty_end_date', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                                <div class="form-group">
                                    {{ Form::label('title', __('messages.assets.asset_manufacturer') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('manufacturer', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('title', __('messages.assets.asset_invoice_number') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('invoice_number', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('title', __('messages.assets.asset_serial_number') . ':') }}
                                    {{ Form::text('serial_number', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    {{ Form::label('image', __('messages.assets.asset_image') . ':') }}
                                    {{ Form::file('image', ['class' => 'form-control', 'id' => 'image']) }}
                                </div>
                                <div class="form-group">
                                    <div id="image-preview" style="display: none;">
                                        <img id="preview-img" src="#" alt="Image Preview" class="circle-image"
                                            style="width:40%; height: auto;border-radius:5px;" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    {{ Form::label('title', __('messages.assets.asset_note') . ':') }}
                                    {{ Form::textarea('asset_note', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription', 'placeholder' => __('messages.assets.asset_note')]) }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('qty', 'Quantity:') }}<span class="required">*</span>
                                        {{ Form::number('qty', 1, ['class' => 'form-control', 'id' => 'qty', 'required', 'min' => 1]) }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('price', 'Price:') }}<span class="required">*</span>
                                        {{ Form::number('price', null, ['class' => 'form-control', 'id' => 'price', 'required', 'step' => '0.01', 'min' => 0]) }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('total', 'Total:') }}
                                        {{ Form::number('total', null, ['class' => 'form-control', 'id' => 'total', 'readonly']) }}
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="text-right mr-3">
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
        let assetCreateUrl = route('assets.store');
        let assetUrl = route('assets.index') + '/';

        $(document).on('submit', '#addAssetForm', function(event) {
            event.preventDefault();
            processingBtn('#addAssetForm', '#btnSave', 'loading');
            var formData = new FormData(this);
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
                        const url = route('assets.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(response.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addAssetForm', '#btnSave');
                },
            });
        });
        $('#image').on('change', function() {
            const previewDiv = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewDiv.style.display = 'block';
                    previewImg.src = e.target.result;
                }

                reader.readAsDataURL(file);
            }
        });

        $(document).ready(function() {
            $('#employee_select').select2({
                width: '100%',
                allowClear: true
            });
            $('#select_category').select2({
                width: '100%',
                allowClear: true
            });

        });

        $(document).on('change', '#purchase_date, #warranty_end_date', function() {
            let purchaseDate = new Date($('#purchase_date').val());
            let warrantyDate = new Date($('#warranty_end_date').val());

            if (warrantyDate < purchaseDate) {
                displayErrorMessage("Warranty end date cannot be before purchase date.");
                $('#warranty_end_date').val('');
            }
        });

        $(document).on('input', '#serial_number', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $(document).on('input', '#qty', function() {
            this.value = this.value.replace(/\D/g, '');
            if (parseInt(this.value) < 1) this.value = 1;
        });

        $(document).on('input', '#price', function() {
            if (parseFloat(this.value) < 0.01) this.value = "0.01";
        });


        function calculateTotal() {
            let qty = parseFloat($('#qty').val()) || 0;
            let price = parseFloat($('#price').val()) || 0;
            let total = qty * price;
            $('#total').val(total.toFixed(2));
        }

        $(document).on('input', '#qty, #price', function() {
            calculateTotal();
        });

        // Run calculation once on page load (if values pre-filled)
        $(document).ready(function() {
            calculateTotal();
        });
    </script>
@endsection
