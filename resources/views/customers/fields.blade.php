<ul class="nav nav-pills mb-3" id="customer" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="customerForm" data-toggle="tab" href="#cForm" role="tab" aria-controls="home"
           aria-selected="true">{{ __('messages.customer.customer_details') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="#addressForm" data-toggle="tab" href="#aForm" role="tab" aria-controls="profile"
           aria-selected="false">{{ __('messages.customer.address') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent2">
    <div class="tab-pane fade show active" id="cForm" role="tabpanel" aria-labelledby="customerForm">
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('company_name', __('messages.customer.company_name').':') }}<span
                        class="required">*</span>
                {{ Form::text('company_name', null, ['class' => 'form-control','required','autocomplete' => 'off','autofocus' => true,'placeholder'=>__('messages.customer.company_name')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('vat_number', __('messages.customer.vat_number').':') }}
                {{ Form::text('vat_number', null, ['class' => 'form-control' ,'minLength' => '4','maxLength' => '15', 'autocomplete' => 'off','placeholder'=>__('messages.customer.vat_number')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('website', __('messages.customer.website').':') }}
                {{ Form::url('website', null, ['class' => 'form-control', 'id' => 'website', 'autocomplete' => 'off','placeholder'=>__('messages.customer.website')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('phone', __('messages.customer.phone').(':')) }} <span
                        class="required">*</span> <br>
                {{ Form::tel('phone', null, ['class' => 'form-control','id' => 'phoneNumber', 'required', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'placeholder'=>__('messages.customer.phone')]) }}
                {{ Form::hidden('prefix_code',old('prefix_code'),['id'=>'prefix_code']) }}
                <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                <span id="error-msg" class="hide"></span>
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('currency', __('messages.customer.currency').':') }}
                <select id="currencyId" data-show-content="true" class="form-control" name="currency">
                    <option value="0"
                            disabled="true" {{ isset($customer->currency) ? '' : 'selected' }}>{{ __('messages.placeholder.select_currency') }}
                    </option>
                    @foreach($data['currencies'] as $key => $currency)
                        <option value="{{$key}}" {{ (isset($customer->currency) ? $customer->currency : getCurrencyIcon($key)) == $key ? 'selected' : ''}}>
                            &#{{getCurrencyIcon($key)}}&nbsp;&nbsp;&nbsp; {{$currency}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('country', __('messages.customer.country').':') }}
                {{ Form::select('country[0]', $data['countries'],(isset($customer) && $customer->country!=null)?$customer->country:null, ['id'=>'countryId','class' => 'form-control','placeholder' => __('messages.placeholder.select_country')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('default_language', __('messages.customer.default_language').':') }}
                {{ Form::select('default_language', $data['languages'],null, ['id'=>'languageId','class' => 'form-control','placeholder' => __('messages.placeholder.select_language')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('groups', __('messages.customer.groups').':') }}
                <div class="input-group">
                    {{ Form::select('groups[]', $data['customerGroups'],isset($customer->customerGroups)?$customer->customerGroups:null, ['id'=>'groupId','class' => 'form-control', 'multiple' => 'multiple']) }}
                    <div class="input-group-append">
                        <div class="input-group-text plus-icon-height">
                            <a href="#" data-toggle="modal" data-target="#customerGroupModal"><i
                                        class="fa fa-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="aForm" role="tabpanel" aria-labelledby="addressForm">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-black-50 font-weight-bold">{{__('messages.customer.billing_address')}}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('street', __('messages.customer.street').':') }}
                        {{ Form::text('street[1]', (isset($data['billingAddress']) && $data['billingAddress']['street']!=null)?$data['billingAddress']['street']:null, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off','placeholder'=>__('messages.customer.street')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('city', __('messages.customer.city').':') }}
                        {{ Form::text('city[1]', (isset($data['billingAddress']) && $data['billingAddress']['city']!=null)?$data['billingAddress']['city']:null, ['class' => 'form-control', 'id' => 'billingCity', 'autocomplete' => 'off','placeholder'=>__('messages.customer.city')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('zip', __('messages.customer.zip_code').':') }}
                        {{ Form::text('zip[1]', (isset($data['billingAddress']) && $data['billingAddress']['zip']!=null)?$data['billingAddress']['zip']:null, ['class' => 'form-control', 'id' => 'billingZip','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off','placeholder'=>__('messages.customer.zip_code')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('state', __('messages.customer.state').':') }}
                        {{ Form::text('state[1]', (isset($data['billingAddress']) && $data['billingAddress']['state']!=null)?$data['billingAddress']['state']:null, ['class' => 'form-control', 'id' => 'billingState', 'autocomplete' => 'off','placeholder'=>__('messages.customer.state')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('country', __('messages.customer.country').':') }}
                        {{ Form::select('country[1]', $data['countries'],(isset($data['billingAddress']) && $data['billingAddress']['country']!=null)?$data['billingAddress']['country']:null, ['id'=>'billingCountryId','class' => 'form-control','placeholder' => __('messages.placeholder.select_country')]) }}
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header text-nowrap">
                        <h4 class="text-black-50 font-weight-bold">
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="{{ __('messages.customer.do_not_fill_shipping_address_customer_invoices') }}"
                               data-original-title="" title=""></i>
                            {{__('messages.customer.shipping_address')}}</h4>
                        <div class="card-header-action">
                            <label class="remove-underline" for="shippingAddressCheck">
                                <a id="copyBillingAddress" class="text-black-50 ">
                                    <input type="checkbox" id="shippingAddressCheck"
                                           class="mr-1" {{ isset($data['shippingAddress']) && $data['shippingAddress']['street'] ? 'checked' : 'disabled' }}>
                                    {{ __('messages.customer.copy_billing_address') }}
                                </a>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('street', __('messages.customer.street').':') }}
                        {{ Form::text('street[2]', (isset($data['shippingAddress']) && $data['shippingAddress']['street']!=null)?$data['shippingAddress']['street']:null, ['class' => 'form-control', 'id' => 'shippingStreet', 'autocomplete' => 'off','placeholder'=>__('messages.customer.street')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('city', __('messages.customer.city').':') }}
                        {{ Form::text('city[2]', (isset($data['shippingAddress']) && $data['shippingAddress']['city']!=null)?$data['shippingAddress']['city']:null, ['class' => 'form-control', 'id' => 'shippingCity', 'autocomplete' => 'off','placeholder'=>__('messages.customer.city')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('zip', __('messages.customer.zip_code').':') }}
                        {{ Form::text('zip[2]', (isset($data['shippingAddress']) && $data['shippingAddress']['zip']!=null)?$data['shippingAddress']['zip']:null, ['class' => 'form-control', 'id' => 'shippingZip','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off','placeholder'=> __('messages.customer.zip_code')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('state', __('messages.customer.state').':') }}
                        {{ Form::text('state[2]', (isset($data['shippingAddress']) && $data['shippingAddress']['state']!=null)?$data['shippingAddress']['state']:null, ['class' => 'form-control', 'id' => 'shippingState', 'autocomplete' => 'off','placeholder'=>__('messages.customer.state')]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('country', __('messages.customer.country').':') }}
                        {{ Form::select('country[2]', $data['countries'],(isset($data['shippingAddress']) && $data['shippingAddress']['country']!=null)?$data['shippingAddress']['country']:null, ['id'=>'shippingCountryId','class' => 'form-control','placeholder' => __('messages.placeholder.select_country')]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12">
            {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary', 'id' => 'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
            <a href="{{ route('customers.index') }}"
               class="btn btn-secondary text-dark">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
</div>
