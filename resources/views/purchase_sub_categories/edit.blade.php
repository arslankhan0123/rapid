@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-sub-categories.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.purchase-sub-categories.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('purchase-sub-categories.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.assets.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $subcategory->id, ['id' => 'category_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.common.name') . ':') }}<span class="required">*</span>
                            {{ Form::text('name', $subcategory->name, ['class' => 'form-control', 'required', 'id' => 'categoryTitle_edit', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('purchase_group_id', __('messages.common.groups') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('purchase_group_id', $groups ?? [], $subcategory->purchase_group_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'type_select', 'placeholder' => __('messages.placeholder.select_group')]) }}
                        </div>

                        <div class="form-group col-sm-12">
                            {{ Form::label('purchase_category_id', __('messages.purchase-sub-categories.name') . ':') }}<span
                                class="required">*</span>
                            <select class="form-control select2" required id="category_select" name="purchase_category_id" >
                                <option value="">{{ __('messages.common.select_category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['id'] }}"
                                        {{ isset($subcategory) && $subcategory->purchase_category_id !== null && $subcategory->purchase_category_id == $category['id'] ? 'selected' : '' }}
                                        data-group-id="{{ $category['purchase_group_id'] }}">{{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.common.description') . ':') }}
                            {{ Form::textarea('description', $subcategory->description, ['class' => 'form-control summernote-simple', 'id' => 'editCategoryDescription']) }}
                        </div>
                    </div>
                    <div class="text-right">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'style' => 'line-height:30px', 'id' => 'btnEditSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

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
        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnEditSave', 'loading');
            let id = $('#category_id').val();

            let description = $('<div />').
            html($('#editCategoryDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#editCategoryDescription').summernote('isEmpty')) {
                $('#editCategoryDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewForm', '#btnEditSave', 'reset');
                return false;
            }

            $.ajax({
                url: route('purchase-sub-categories.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('purchase-sub-categories.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editForm', '#btnEditSave');
                },
            });
        });
    </script>

    <script>
        // Convert only categories to JSON
        var categories = @json($categories);
        $(document).ready(function() {
            // Initialize Select2 for the group dropdown
            $('#type_select').select2();

            // When the group is selected, rebuild the category options
            $('#type_select').on('change', function() {
                var selectedGroupId = $(this).val(); // Get the selected group ID

                // Reset and clear the category dropdown
                $('#category_select').empty().append(
                    '<option value="">{{ __('messages.common.select_category') }}</option>');

                // Rebuild the category options based on the selected group
                categories.forEach(function(category) {
                    if (selectedGroupId === "" || selectedGroupId == category.purchase_group_id) {
                        $('#category_select').append('<option value="' + category.id + '">' +
                            category.name + '</option>');
                    }
                });

                // Refresh the Select2 dropdown
                $('#category_select').select2();
            });

        });
    </script>
@endsection
