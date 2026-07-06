@extends('layouts.app')
@section('title')
    {{ __('messages.products.unit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.assets.edit_asset') }}</h1>
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
                <div class="card-body">


                    {{ Form::open(['id' => 'editFormNew', 'enctype' => 'multipart/form-data']) }}
                    {{ Form::hidden('id', $asset->id, ['id' => 'asset_id']) }}


                    <div class="row">
                        <div class="form-group  col-md-6">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], $asset->branch_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('name', __('messages.assets.asset_name') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::text('name', $asset->name, ['class' => 'form-control', 'required', 'id' => 'edit_name', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('title', __('messages.assets.asset_company_code') . ':') }}
                                {{ Form::text('company_asset_code', $asset->company_asset_code, ['class' => 'form-control', 'id' => 'edit_company_asset_code', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.asset_company_code')]) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('category_id', __('messages.assets.category') . ':') }}
                                {{ Form::select('asset_category_id', $categories, $asset->asset_category_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'edit_asset_category_id']) }}
                            </div>

                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('is_working', __('messages.assets.is_working') . ':') }}
                                {{ Form::select('is_working', ['yes' => 'Yes', 'no' => 'No'], $asset->is_working, ['class' => 'form-control', 'id' => 'edit_is_working', 'autocomplete' => 'off']) }}
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
                                {{ Form::select('employee_id', $employees, $asset->employee_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'edit_employee_id', 'placeholder' => __('messages.assets.select_employee')]) }}
                            </div>

                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('employee_id', 'Supplier' . ':') }}
                                {{ Form::select('supplier_id', $suppliers ?? [], $asset->supplier_id, ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Select Supplier']) }}
                            </div>

                        </div>

                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('purchase_date', __('messages.assets.asset_purchase_date') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::date('purchase_date', $asset->purchase_date, ['class' => 'form-control', 'required', 'id' => 'edit_purchase_date', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('warranty_end_date', __('messages.assets.asset_warranty_end_date') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::date('warranty_end_date', $asset->warranty_end_date, ['class' => 'form-control', 'required', 'id' => 'edit_warranty_end_date', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.asset_warranty_end_date')]) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('title', __('messages.assets.asset_manufacturer') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::text('manufacturer', $asset->manufacturer, ['class' => 'form-control', 'required', 'id' => 'edit_manufacturer', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.asset_manufacturer')]) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('title', __('messages.assets.asset_invoice_number') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::text('invoice_number', $asset->invoice_number, ['class' => 'form-control', 'id' => 'edit_invoice_number', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.asset_invoice_number')]) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('title', __('messages.assets.asset_serial_number') . ':') }}
                                {{ Form::text('serial_number', $asset->serial_number, ['class' => 'form-control', 'id' => 'edit_serial_number', 'autocomplete' => 'off', 'placeholder' => __('messages.assets.asset_serial_number')]) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->

                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('image', __('messages.assets.asset_image') . ':') }}
                                {{ Form::file('image', ['class' => 'form-control', 'id' => 'edit_image']) }}
                            </div>
                            <div class="form-group">


                                <div id="edit_image-preview" style="display: {{ $asset->image ?? 'none' }} ;">
                                    <img id="edit_preview-img" src="{{ asset('uploads/public/images/' . $asset->image) }}"
                                        alt="Image Preview" class="circle-image"
                                        style="width:40%; height: auto;border-radius:5px;" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('title', __('messages.assets.asset_note') . ':') }}
                                {{ Form::textarea('asset_note', $asset->asset_note, ['class' => 'form-control summernote-simple', 'id' => 'edit_description', 'placeholder' => __('messages.assets.asset_note')]) }}

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('qty', 'Quantity:') }}<span class="required">*</span>
                                    {{ Form::number('qty', $asset->qty, ['class' => 'form-control', 'id' => 'edit_qty', 'required', 'min' => 1]) }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('price', 'Price:') }}<span class="required">*</span>
                                    {{ Form::number('price', $asset->price, ['class' => 'form-control', 'id' => 'edit_price', 'required', 'step' => '0.01', 'min' => 0]) }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('total', 'Total:') }}
                                    {{ Form::number('total', $asset->total, ['class' => 'form-control', 'id' => 'edit_total', 'readonly']) }}
                                </div>
                            </div>
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
                url: route('assets.update', id),
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('assets.index', );
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

        $('#edit_image').on('change', function() {
            const previewDiv = document.getElementById('edit_image-preview');
            const previewImg = document.getElementById('edit_preview-img');
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

        function calculateEditTotal() {
            let qty = parseFloat($('#edit_qty').val()) || 0;
            let price = parseFloat($('#edit_price').val()) || 0;
            let total = qty * price;
            $('#edit_total').val(total.toFixed(2));
        }

        $(document).on('input', '#edit_qty, #edit_price', function() {
            calculateEditTotal();
        });

        // Run calculation once on page load (prefill)
        $(document).ready(function() {
            calculateEditTotal();
        });
    </script>
@endsection
