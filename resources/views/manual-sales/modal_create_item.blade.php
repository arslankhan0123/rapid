<div id="addItemModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.products.new_product') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'addItemNewForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <div class="row">

                    <div class="form-group col-sm-12">
                        {{ Form::label('title', __('messages.products.title') ) }}<span class="required">*</span>
                        {{ Form::text('title', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'maxlength' => 300]) }}
                    </div>
                    <div class="form-group col-sm-12 mt-2">
                        {{ Form::label('title', __('messages.service_categories.select') ) }}<span
                            class="required">*</span>
                         <br>
                        {{ Form::select('item_group_id', $categories, null, ['class' => 'form-control select2', 'required', 'id' => 'serviceGroup' ,'style'=>"width:100%;"]) }}
                    </div>


                    <div class="form-group col-sm-12 col-lg-6 col-md-12">

                        {{ Form::hidden('rate', 0, ['class' => 'form-control price-input', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.products.rate')]) }}
                    </div>
                </div>


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
