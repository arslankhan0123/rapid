<div id="customeraddModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.customer.add') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'addCustomerForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>

                <div id="cForm" role="tabpanel" aria-labelledby="customerForm">
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('code', __('messages.customer.code')) }}<span class="required">*</span>
                            {{ Form::text('code', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus' => true]) }}
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            {{ Form::label('company_name', __('messages.customer.company_name')) }}<span
                                class="required">*</span>
                            {{ Form::text('company_name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus' => true]) }}
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('vat_number', __('messages.customer.vat_number')) }}
                            {{ Form::text('vat_number', null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('phone', __('messages.customer.phone')) }}<br>
                            {{ Form::tel('phone', null, ['class' => 'form-control', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                            {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
                            <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                            <span id="error-msg" class="hide"></span>
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('mobile', __('messages.customer.mobile')) }}
                            {{ Form::tel('mobile', $customer->mobile ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '30', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('email', __('messages.customer.email')) }}
                            {{ Form::email('email', $customer->email ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '30', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('website', __('messages.customer.website')) }}
                            {{ Form::url('website', null, ['class' => 'form-control', 'id' => 'website', 'autocomplete' => 'off']) }}
                        </div>


                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('country', __('messages.customer.country')) }}<span
                                class="required">*</span>
                            {{ Form::select(
                                'country',
                                $data['countries'],
                                isset($data['billingAddress']) && $data['billingAddress']['country'] != null
                                    ? $data['billingAddress']['country']
                                    : null,
                                [
                                    'id' => 'billingCountryId',
                                    'class' => 'form-control',
                                    'required',
                                    'placeholder' => __('messages.placeholder.select_country'),
                                ],
                            ) }}
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('state', __('messages.customer.state')) }}<span class="required">*</span>
                            {{ Form::select('state', [], null, ['id' => 'billingState', 'required', 'class' => 'form-control']) }}
                            <!-- Keep initial state dropdown empty -->
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('city', __('messages.customer.city')) }}
                            {{ Form::text('city', isset($data['billingAddress']) && $data['billingAddress']['city'] != null ? $data['billingAddress']['city'] : null, ['class' => 'form-control', 'id' => 'billingCity', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('street', __('messages.customer.street')) }}
                            {{ Form::text('street', isset($data['billingAddress']) && $data['billingAddress']['street'] != null ? $data['billingAddress']['street'] : null, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off']) }}
                        </div>

                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('zip', __('messages.customer.zip_code')) }}
                            {{ Form::text('zip', isset($data['billingAddress']) && $data['billingAddress']['zip'] != null ? $data['billingAddress']['zip'] : null, ['class' => 'form-control', 'id' => 'billingZip', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off']) }}
                        </div>

                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('currency', __('messages.customer.currency')) }}<span
                                class="required">*</span>
                            {{ Form::select('currency', $currencies, null, ['id' => 'currencyId', 'required', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_currency')]) }}


                        </div>

                        <div class="form-group col-md-3 col-sm-12">
                            {{ Form::label('default_language', __('messages.customer.default_language')) }}<span
                                class="required">*</span>
                            {{ Form::select('default_language', $data['languages'], null, ['id' => 'languageId', 'required', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_language')]) }}
                        </div>


                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnCustomerSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" id="btnCancel" class="btn btn-light ml-1"
                        data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
