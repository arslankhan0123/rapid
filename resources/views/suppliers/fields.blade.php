<div class="tab-content" id="myTabContent2">
    <div class="tab-pane fade show active" id="cForm" role="tabpanel" aria-labelledby="customerForm">
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('company_name', __('messages.suppliers.name') . ':') }}<span class="required">*</span>
                {{ Form::text('company_name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus' => true, 'placeholder' => __('messages.suppliers.name')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('vat_number', __('messages.customer.vat_number') . ':') }}
                {{ Form::text('vat_number', null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.vat_number')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('website', __('messages.customer.website') . ':') }}
                {{ Form::url('website', null, ['class' => 'form-control', 'id' => 'website', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.website')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('phone', __('messages.customer.phone') . ':') }}<br>
                {{ Form::tel('phone', null, ['class' => 'form-control', 'id' => 'phoneNumber', 'placeholder' => __('messages.customer.phone')]) }}
                {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
                <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                <span id="error-msg" class="hide"></span>
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('currency', __('messages.customer.currency') . ':') }}
                <select id="currencyId" data-show-content="true" class="form-control" name="currency" required>
                    <option value="" disabled selected>{{ __('messages.customer.select_currency') }}</option>

                    @foreach ($data['currencies'] as $key => $currency)
                        <option value="{{ $key }}"
                            {{ (isset($customer->currency) ? $customer->currency : getCurrencyIcon($key)) == $key ? 'selected' : '' }}>
                            {{ $currency }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('country', __('messages.customer.country') . ':') }}
                {{ Form::select('country', $data['countries'], isset($customer) && $customer->country != null ? $customer->country : null, ['id' => 'countryId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_country')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('default_language', __('messages.customer.default_language') . ':') }}
                {{ Form::select('default_language', $data['languages'], null, ['id' => 'languageId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_language')]) }}
            </div>
            {{-- <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('groups', __('messages.customer.groups') . ':') }}
                <div class="input-group">
                    {{ Form::select('groups[]', $data['supplierGroups'], isset($customer->supplierGroups) ? $customer->supplierGroups : null, ['id' => 'groupId', 'class' => 'form-control', 'multiple' => 'multiple']) }}
                </div>
            </div> --}}
            {{-- billing address --}}
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('street', __('messages.customer.street') . ':') }}
                {{ Form::text('street', null, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.street')]) }}
            </div>
            {{-- <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('city', __('messages.customer.city') . ':') }}
                {{ Form::text('city', null, ['class' => 'form-control', 'id' => 'billingCity', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.city')]) }}
            </div> --}}
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('zip', __('messages.customer.zip_code') . ':') }}
                {{ Form::text('zip', null, ['class' => 'form-control', 'id' => 'billingZip', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.zip_code')]) }}
            </div>
            {{-- <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('state', __('messages.customer.state') . ':') }}
                {{ Form::text('state', null, ['class' => 'form-control', 'id' => 'billingState', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.state')]) }}

            </div> --}}
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('opening_balance', 'Opening Balance') }}
                {{ Form::number('opening_balance', 0, ['class' => 'form-control', 'step' => 'Any']) }}
            </div>


        </div>
        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">

            <div class="row">
                <div class="col">
                    <h3>{{ __('messages.employees.documents') }}</h3>
                </div>

                <div class="col">
                    <button type="button" id="addField" class="btn btn-primary mb-3 float-right"><i class="fa fa-plus"
                            aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <div id="formContainer" class="mb-3">
            <div class="form-group row mb-3">
                <div class="col-md-4">
                    <label for="doc_name" class="form-label">{{ __('messages.employees.documents') }}</label>
                    <input type="text" name="doc_name[]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }},
                        {{ __('messages.employees.max_size') }}</label>
                    <input type="file" name="file[]" class="form-control" accept="application/pdf">
                </div>
                <div class="col-md-3">
                    <label for="expiry_date" class="form-label">{{ __('messages.employees.expiry_date') }}</label>
                    <input type="date" name="expiry_date[]" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger removeField" style="display:none;">Remove</button>
                </div>
            </div>
        </div>
        <div class="form-group " style="float:right;">
            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        </div>
    </div>

</div>
