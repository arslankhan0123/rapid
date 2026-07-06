<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.products.new_product') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'addNewForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <div class="row">

                    <div class="form-group col-sm-12">
                        {{ Form::label('title', __('messages.products.title') . ':') }}<span class="required">*</span>
                        {{ Form::text('title', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'maxlength' => 300]) }}
                    </div>
                    <div class="form-group col-sm-12">
                        {{ Form::label('title_arabic', 'Arabic Service Name') }}
                        {{ Form::text('title_arabic', null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => 300]) }}
                    </div>
                    <div class="form-group col-sm-12">
                        {{ Form::label('title', __('messages.service_categories.select') . ':') }}<span
                            class="required">*</span>
                        {{ Form::select('item_group_id', $categories, null, ['class' => 'form-control', 'required', 'id' => 'serviceGroup']) }}
                    </div>

                    {{-- <div class="form-group col-sm-12 mb-0">
                        {{ Form::label('description', __('messages.common.description') . ':') }}
                        {{ Form::textarea('description', null, ['class' => 'form-control textarea-sizing', 'id' => 'productDescription']) }}
                    </div> --}}
                    <div class="form-group col-sm-12 col-lg-6 col-md-12">
                        {{-- {{ Form::label('rate ', __('messages.products.rate').':') }}<span class="required">*</span>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="{{getCurrencyClass()}}"></i>
                                </div>
                            </div> --}}
                        {{ Form::hidden('rate', 0, ['class' => 'form-control price-input', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.products.rate')]) }}
                    </div>
                </div>


                {{-- <div class="form-group col-sm-12 col-lg-6 col-md-12">
                        {{ Form::label('productGroup ', __('messages.products.product_group').':') }}<span
                                class="required">*</span>
                        <div class="input-group">
                            {{ Form::select('item_group_id', $data['itemGroups'],$data['itemGroups'], ['class' => 'form-control', 'id' => 'productGroup', 'required','placeholder' => __('messages.placeholder.select_product_group')]) }}
                            <div class="input-group-append plus-icon-height">
                                <div class="input-group-text">
                                    <a href="#" data-toggle="modal" data-target="#addProductGroupModal"><i
                                                class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                {{-- <div class="form-group col-sm-12 col-lg-6 col-md-12">
                        {{ Form::label('tax_1 ', __('messages.products.tax').' 1:') }}
                        {{ Form::select('tax_1_id', $data['taxes'],$data['taxes'] , ['class' => 'form-control', 'id' => 'taxSelectOne', 'placeholder' => __('messages.placeholder.select_tax1')]) }}
                    </div>
                    <div class="form-group col-sm-12 col-lg-6 col-md-12">
                        {{ Form::label('tax_2 ', __('messages.products.tax').' 2:') }}
                        {{ Form::select('tax_2_id', $data['taxes'],$data['taxes'], ['class' => 'form-control', 'id' => 'taxSelectTwo', 'placeholder' => __('messages.placeholder.select_tax2')]) }}
                    </div> --}}
            </div>

            <div class="text-right m-4">
                <button type="button" id="btnCancel" class="btn btn-secondary  ml-1"
                    style="height: 40px;font-weigth:bold;"
                    data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>
