@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-items.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.purchase-items.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('purchase-items.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'addNewForm', 'enctype' => 'multipart/form-data']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('title', __('messages.pos.item_code')) }}<span class="required">*</span>
                            {{ Form::text('code', $nextNumber, ['class' => 'form-control', 'required', 'autocomplete' => 'off', $itemCodeStatus == 1 ? 'readonly' : null]) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('title', __('messages.common.barcode')) }}<span class="required">*</span>
                            {{ Form::text('barcode', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'maxlength' => '15']) }}
                        </div>

                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.short_name')) }}
                            {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'productUnit_title', 'autocomplete' => 'off', 'maxlength' => '15']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.full_name')) }}<span
                                class="required">*</span>
                            {{ Form::text('full_name', null, ['class' => 'form-control', 'required', 'id' => 'categoryTitle_edit', 'autocomplete' => 'off', 'maxLength' => 40]) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.cost_price')) }}
                            {{ Form::number('cost_price', null, ['class' => 'form-control', 'autocomplete' => 'off', 'step' => 'any']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.price')) }}<span class="required">*</span>
                            {{ Form::number('price', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'step' => 'any']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.stock')) }}
                            {{ Form::number('stock', $item->stock ?? 0, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.common.groups')) }}<span
                                class="required">*</span>
                            {{ Form::select('purchase_group_id', $groups ?? [], null, ['class' => 'form-control select2', 'required', 'id' => 'type_select', 'placeholder' => __('messages.placeholder.select_group')]) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_category_id', __('messages.purchase-categories.name')) }}
                            <span class="required">*</span>
                            <select class="form-control select2" id="category_select" name="purchase_category_id" required>
                                <option value="">{{ __('messages.common.select_category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['id'] }}"
                                        data-group-id="{{ $category['purchase_group_id'] }}">{{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_sub_category_id', __('messages.purchase-sub-categories.name')) }}
                            <select class="form-control select2" id="subcategory_select" name="purchase_sub_category_id">
                                <option value="">{{ __('messages.common.select_sub_category') }}</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory['id'] }}"
                                        data-category-id="{{ $subcategory['purchase_category_id'] }}">
                                        {{ $subcategory['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.unit')) }}<span
                                class="required">*</span>
                            {{ Form::select('unit_id', $units ?? [], null, ['class' => 'form-control select2', 'required', 'id' => 'unit_select']) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.brand')) }}
                            {{ Form::select('brand_id', $brands ?? [], null, ['class' => 'form-control select2']) }}
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.size')) }}
                            {{ Form::select('size_id', $sizes ?? [], null, ['class' => 'form-control select2']) }}
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.color')) }}
                            {{ Form::select('color_id', $colors ?? [], null, ['class' => 'form-control select2']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6 mb-0">
                            {{ Form::label('description', __('messages.purchase-items.image')) }} </br>
                            <input type="file" name="image" id="image">
                        </div>
                        <div class="form-group col-sm-12 col-md-12 mb-0">
                            {{ Form::label('description', __('messages.assets.category_description')) }}
                            {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                        </div>


                    </div>
                    <div class="text-right">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'style' => 'line-height:30px', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

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
        let categoryCreateUrl = route('purchase-items.store');
        $(document).on('submit', '#addNewForm', function(event) {
            event.preventDefault();
            processingBtn('#addNewForm', '#btnSave', 'loading');

            let formData = new FormData(this);

            let description = $('<div />').html($('#createDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#createDescription').summernote('isEmpty')) {
                formData.set('description', '');
            } else if (empty) {
                displayErrorMessage('Description field cannot contain only white space');
                processingBtn('#addNewForm', '#btnSave', 'reset');
                return false;
            }

            $.ajax({
                url: categoryCreateUrl,
                type: 'POST',
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('#addNewForm')[0].reset();
                        window.location.href = route('purchase-items.index');
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewForm', '#btnSave');
                },
            });
        });
    </script>


    <script>
        // Convert categories and subcategories to JSON
        var categories = @json($categories);
        var subcategories = @json($subcategories);

        $(document).ready(function() {
            // Initialize Select2 for the group, category, and subcategory dropdowns
            $('#type_select, #category_select, #subcategory_select').select2();

            // When the group is selected, rebuild the category and subcategory options
            $('#type_select').on('change', function() {
                var selectedGroupId = $(this).val(); // Get the selected group ID

                // Reset and clear the category dropdown
                $('#category_select').empty().append(
                    '<option value="">{{ __('messages.common.select_category') }}</option>');

                // Reset and clear the subcategory dropdown
                $('#subcategory_select').empty().append(
                    '<option value="">{{ __('messages.common.select_sub_category') }}</option>');

                // Rebuild the category options based on the selected group
                categories.forEach(function(category) {
                    if (selectedGroupId === "" || selectedGroupId == category.purchase_group_id) {
                        $('#category_select').append('<option value="' + category.id + '">' +
                            category.name + '</option>');
                    }
                });

                // Refresh the Select2 dropdowns for both category and subcategory
                $('#category_select, #subcategory_select').select2();
            });

            // When the category is selected, rebuild the subcategory options
            $('#category_select').on('change', function() {
                var selectedCategoryId = $(this).val(); // Get the selected category ID

                // Reset and clear the subcategory dropdown
                $('#subcategory_select').empty().append(
                    '<option value="">{{ __('messages.common.select_sub_category') }}</option>');

                // Rebuild the subcategory options based on the selected category
                subcategories.forEach(function(subcategory) {
                    if (selectedCategoryId === "" || selectedCategoryId == subcategory
                        .purchase_category_id) {
                        $('#subcategory_select').append('<option value="' + subcategory.id + '">' +
                            subcategory.name + '</option>');
                    }
                });

                // Refresh the Select2 dropdown for subcategories
                $('#subcategory_select').select2();
            });
        });
    </script>
@endsection
