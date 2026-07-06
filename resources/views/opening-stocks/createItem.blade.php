<div id="createNewItem" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 60%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.purchase-items.add') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'addNewItemToInventory']) }}
            <div class="modal-body">

                <div class="container-fluid">
                    <div class="row ">
                        <div class="form-group col-sm-12 col-md-12">
                            {{ Form::label('title', __('messages.common.name') . ':') }}<span
                                class="required">*</span><br>
                            {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'productUnit_title', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.common.groups') . ':') }}<span
                                class="required">*</span><br>
                            {{ Form::select('purchase_group_id', $groups ?? [], null, ['class' => 'form-control', 'required', 'id' => 'type_select', 'placeholder' => __('messages.placeholder.select_group')]) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_category_id', __('messages.purchase-categories.name') . ':') }}<span
                                class="required">*</span>
                            <select class="form-control " required id="category_select"
                                name="purchase_category_id">
                                <option value="">{{ __('messages.common.select_category') }}</option>
                                @foreach ($purchaseCategories as $category)
                                    <option value="{{ $category['id'] }}"
                                        data-group-id="{{ $category['purchase_group_id'] }}">{{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_sub_category_id', __('messages.purchase-sub-categories.name') . ':') }}<span
                                class="required">*</span>
                            <select class="form-control " required id="subcategory_select"
                                name="purchase_sub_category_id">
                                <option value="">{{ __('messages.common.select_sub_category') }}</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory['id'] }}"
                                        data-category-id="{{ $subcategory['purchase_category_id'] }}">
                                        {{ $subcategory['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('purchase_group_id', __('messages.purchase-items.unit') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('unit_id', $units ?? [], null, ['class' => 'form-control ', 'required', 'id' => 'unit_select']) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('title', __('messages.common.code') . ':') }}<span class="required">*</span>
                            {{ Form::text('code', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            {{ Form::label('title', __('messages.common.barcode') . ':') }}
                            {{ Form::text('barcode', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-12 mb-0">
                            {{ Form::label('description', __('messages.assets.category_description') . ':') }}
                            {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                        </div>

                    </div>
                </div>
                <div class="text-right">
                    <button type="button" id="btnCancel" class="btn btnWarning btn-light text-white ml-1"
                        style="line-height: 30px;" data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'style' => 'line-height:30px', 'id' => 'createNewItem', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
