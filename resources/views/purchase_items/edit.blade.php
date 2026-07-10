@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-items.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.purchase-items.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('purchase-items.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.assets.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm', 'enctype' => 'multipart/form-data']) }}
                    {{ Form::hidden('id', $item->id, ['id' => 'category_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('title', __('messages.pos.item_code')) }}<span class="required">*</span>
                            {{ Form::text('code', $item->code ?? null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'readonly' => 'readonly']) }}
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('title', __('messages.common.barcode')) }}<span class="required">*</span>
                            {{ Form::text('barcode', $item->barcode ?? null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'maxlength' => '15']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.short_name')) }}<span
                                class="required">*</span>
                            {{ Form::text('name', $item->name, ['class' => 'form-control', 'required', 'id' => 'categoryTitle_edit', 'autocomplete' => 'off', 'maxLength' => 20]) }}
                        </div> --}}
                        <div class="form-group col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.full_name')) }}<span
                                class="required">*</span>
                            {{ Form::text('full_name', $item->full_name ?? '', ['class' => 'form-control', 'required', 'id' => 'categoryTitle_edit', 'autocomplete' => 'off', 'maxLength' => 40]) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.cost_price')) }}
                            {{ Form::number('cost_price', $item->cost_price ?? 0, ['class' => 'form-control', 'autocomplete' => 'off', 'step' => 'any']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.price')) }}<span class="required">*</span>
                            {{ Form::number('price', $item->price, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'step' => 'any']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('title', __('messages.purchase-items.stock')) }}
                            {{ Form::number('stock', $item->stock ?? 0, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.common.groups')) }}<span
                                class="required">*</span>
                            {{ Form::select('purchase_group_id', $groups ?? [], $item->purchase_group_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'type_select', 'placeholder' => __('messages.placeholder.select_group')]) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_category_id', __('messages.purchase-categories.name')) }}<span
                                class="required">*</span>
                            <select class="form-control select2" id="category_select" name="purchase_category_id" required>
                                <option value="">{{ __('messages.common.select_category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['id'] }}"
                                        {{ isset($item) && $item->purchase_category_id !== null && $item->purchase_category_id == $category['id'] ? 'selected' : '' }}
                                        data-group-id="{{ $category['purchase_group_id'] }}">{{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_sub_category_id', __('messages.purchase-items.name')) }}
                            <select class="form-control select2" id="subcategory_select" name="purchase_sub_category_id">
                                <option value="">{{ __('messages.common.select_sub_category') }}</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory['id'] }}"
                                        {{ isset($item) && $item->purchase_sub_category_id !== null && $item->purchase_sub_category_id == $subcategory['id'] ? 'selected' : '' }}
                                        data-category-id="{{ $subcategory['purchase_category_id'] }}">
                                        {{ $subcategory['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.unit')) }}<span
                                class="required">*</span>
                            {{ Form::select('unit_id', $units ?? [], $item->unit_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'unit_select']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.brand')) }}
                            {{ Form::select('brand_id', $brands ?? [], $item->brand_id ?? null, ['class' => 'form-control select2']) }}
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.size')) }}
                            {{ Form::select('size_id', $sizes ?? [], $item->size_id ?? null, ['class' => 'form-control select2']) }}
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.color')) }}
                            {{ Form::select('color_id', $colors ?? [], $item->color_id ?? null, ['class' => 'form-control select2']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6 mb-0">
                            {{ Form::label('description', __('messages.purchase-items.image')) }} </br>
                            <input type="file" name="image" id="image">
                            @if($item->image)
                                <br>
                                <a href="javascript:void(0)" class="view-image-btn" data-image-url="{{ asset($item->image) }}" data-title="{{ $item->full_name ?? $item->name ?? 'Image' }}">
                                    <img src="{{ asset($item->image) }}" alt="Item Image" width="100" style="object-fit:cover; border-radius:4px; margin-top: 10px;">
                                </a>
                            @endif
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('specifications', 'Specifications') }}
                            {{ Form::text('specifications', $item->specifications ?? null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => '255']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('processor', 'Processor') }}
                            {{ Form::text('processor', $item->processor ?? null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => '255']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('ram', 'RAM') }}
                            {{ Form::text('ram', $item->ram ?? null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => '255']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('storage', 'Storage') }}
                            {{ Form::text('storage', $item->storage ?? null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => '255']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('casing', 'Casing') }}
                            {{ Form::text('casing', $item->casing ?? null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => '255']) }}
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.common.description')) }}
                            {{ Form::textarea('description', $item->description, ['class' => 'form-control summernote-simple', 'id' => 'editDescription']) }}
                        </div>
                    </div>
                    <div class="text-right">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'style' => 'line-height:30px', 'id' => 'btnEditSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                    </div>

                    {{ Form::close() }}

                </div>
            </div>
        </div>
        
        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" id="previewModalImage" class="img-fluid" alt="Preview Image" style="max-height: 400px; object-fit: contain;">
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
    <script>
        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnEditSave', 'loading');

            let id = $('#category_id').val();
            let formData = new FormData(this); // Move FormData outside

            let description = $('<div />').html($('#editDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#editDescription').summernote('isEmpty')) {
                formData.set('description', '');
            } else if (empty) {
                displayErrorMessage('Description field cannot contain only white space');
                processingBtn('#editForm', '#btnEditSave', 'reset');
                return false;
            }

            $.ajax({
                url: route('purchase-items.update', id),
                type: 'POST', // Laravel requires POST for file uploads, with `_method` as PUT
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                }, // Spoof PUT method for Laravel
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        window.location.href = route('purchase-items.index');
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

        $(document).on('click', '.view-image-btn', function() {
            let imageUrl = $(this).data('image-url');
            let title = $(this).data('title');
            $('#imagePreviewModalLabel').text(title);
            $('#previewModalImage').attr('src', imageUrl);
            $('#imagePreviewModal').appendTo("body").modal('show');
        });
    </script>
@endsection
