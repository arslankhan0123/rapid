<div class="tab-content" id="myTabContent2">
    <div class="tab-pane fade show active" id="cForm" role="tabpanel" aria-labelledby="customerForm">
        <div class="row">
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('code', __('messages.customer.code')) }}<span class="required">*</span>
                {{ Form::text('code', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus' => true]) }}
            </div>

            <div class="form-group col-md-4 col-sm-12">
                {{ Form::label('company_name', __('messages.customer.company_name')) }}<span class="required">*</span>
                {{ Form::text('company_name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus' => true]) }}
            </div>

            <div class="form-group col-md-2 col-sm-12">
                {{ Form::label('customer_arabic_name', 'Arabic Name') }}
                {{ Form::text('customer_arabic_name', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
            </div>

            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('short_name', __('messages.customer.short_name')) }}
                {{ Form::text('short_name', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
            </div>

            <div class="form-group col-md-3 mb-0 ">
                {{ Form::label('status', __('messages.customer.inactive')) }} <br>
                <div class="form-check form-switch" style="padding:0px;">
                    {{ Form::checkbox('inactive', 1, $customer->inactive ?? false, ['class' => 'form-check-input ml-4', 'id' => 'statusSwitch']) }}
                </div>
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('vat_number', __('messages.customer.vat_number')) }}
                {{ Form::text('vat_number', null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('vendor_code', __('messages.customer.vendor_code')) }}
                {{ Form::text('vendor_code', null, ['class' => 'form-control', 'maxLength' => '10', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-3  col-sm-12">
                {{ Form::label('payment_modes', __('messages.customer.payment_mode') . ':') }}
                <span class="required">*</span>
                <div class="input-group">
                    {{ Form::select('payment_modes[]', $data['paymentModes'], isset($customer) ? ($customer->customerPayment ? $customer->customerPayment->pluck('payment_id') : null) : null, ['class' => 'form-control', 'id' => 'paymentModes', 'autocomplete' => 'off', 'multiple' => 'multiple', 'required']) }}

                </div>
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('currency', __('messages.customer.currency')) }}<span class="required">*</span>
                {{ Form::select('currency', $currencies, null, ['id' => 'currencyId', 'required', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_currency')]) }}

                {{-- <select id="currencyId" data-show-content="true" class="form-control" name="currency">
                    <option value="0" disabled="true" {{ isset($customer->currency) ? '' : 'selected' }}>
                        {{ __('messages.placeholder.select_currency') }}
                    </option>
                    @foreach ($data['currencies'] as $key => $currency)
                        <option value="{{ $key }}"
                            {{ (isset($customer->currency) ? $customer->currency : getCurrencyIcon($key)) == $key ? 'selected' : '' }}>
                            &#{{ getCurrencyIcon($key) }}&nbsp;&nbsp;&nbsp; {{ $currency }}
                        </option>
                    @endforeach
                </select> --}}
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('phone', __('messages.customer.phone')) }} <span class="required">*</span><br>
                {{ Form::tel('phone', null, ['class' => 'form-control', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
                <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                <span id="error-msg" class="hide"></span>
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('fax', __('messages.customer.fax')) }}
                {{ Form::tel('fax', $customer->fax ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '30', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('mobile', __('messages.customer.mobile')) }}
                {{ Form::tel('mobile', $customer->mobile ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '30', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('whatsapp', __('messages.customer.whatsapp')) }}
                {{ Form::tel('whatsapp', $customer->whatsapp ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '30', 'autocomplete' => 'off']) }}
            </div>



            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('country', __('messages.customer.country')) }}<span class="required">*</span>
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
            {{-- <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('city', __('messages.customer.city')) }}
                {{ Form::text('city', isset($data['billingAddress']) && $data['billingAddress']['city'] != null ? $data['billingAddress']['city'] : null, ['class' => 'form-control', 'id' => 'billingCity', 'autocomplete' => 'off']) }}
            </div> --}}
            {{-- <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('street', __('messages.customer.street')) }}
                {{ Form::text('street', isset($data['billingAddress']) && $data['billingAddress']['street'] != null ? $data['billingAddress']['street'] : null, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off']) }}
            </div> --}}

            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('zip', __('messages.customer.zip_code')) }}
                {{ Form::text('zip', isset($data['billingAddress']) && $data['billingAddress']['zip'] != null ? $data['billingAddress']['zip'] : null, ['class' => 'form-control', 'id' => 'billingZip', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off']) }}
            </div>


            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('address', __('messages.customer.address')) }}
                {{ Form::text('address', $customer->address ?? null, ['class' => 'form-control', 'maxLength' => '200', 'autocomplete' => 'off']) }}
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
                {{ Form::label('default_language', __('messages.customer.default_language')) }}<span
                    class="required">*</span>
                {{ Form::select('default_language', $data['languages'], null, ['id' => 'languageId', 'required', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_language')]) }}
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('bank_id', __('messages.customer.bank')) }}
                {{ Form::select('bank_id', $banks, null, ['class' => 'form-control', 'id' => 'bankId', 'placeholder' => __('messages.placeholder.select_bank')]) }}
            </div>

            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('location_url', __('messages.customer.location_url')) }}
                {{ Form::text('location_url', $customer->address ?? null, ['class' => 'form-control', 'maxLength' => '30', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('opening_balance', 'Opening Balance') }}
                {{ Form::number('opening_balance', $customer->opening_balance ?? 0, ['class' => 'form-control', 'step' => 'Any']) }}
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <div class="row">
                    {{ Form::label('location_url', __('messages.customer.customer_logo')) }}
                    <div class="col-md-6">

                        <div id="drop-area">

                            Drag & Drop Customer Logo Here or Click to Upload
                        </div>
                    </div>
                    <div class="col-md-4">
                        {{ Form::file('customer_logo', ['id' => 'file-input', 'accept' => 'image/*']) }}

                        @if (isset($customer) && $customer->customer_logo)
                            <img id="preview" src="{{ asset('uploads/customer/' . $customer->customer_logo) }}"
                                alt="Customer Logo Preview" style="max-width: 200px; height: 200px;" class="rounded">
                        @else
                            <img id="preview" src="https://placehold.co/200" alt="Placeholder Logo"
                                style="max-width: 200px; height: 200px;" class="rounded">
                        @endif

                    </div>
                </div>
            </div>


            {{-- <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('country', __('messages.customer.country') ) }}
                {{ Form::select('country[0]', $data['countries'], isset($customer) && $customer->country != null ? $customer->country : null, ['id' => 'countryId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_country')]) }}
            </div> --}}

            {{-- <div class="form-group col-md-3 col-sm-12">
                {{ Form::label('groups', __('messages.customer.groups') ) }}
                <div class="input-group">
                    {{ Form::select('groups[]', $data['customerGroups'], isset($customer->customerGroups) ? $customer->customerGroups : null, ['id' => 'groupId', 'class' => 'form-control', 'multiple' => 'multiple']) }}
                    <div class="input-group-append">
                        <div class="input-group-text plus-icon-height">
                            <a href="#" data-toggle="modal" data-target="#customerGroupModal"><i
                                    class="fa fa-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div> --}}


            <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                <h3>{{ __('messages.employees.manage_documents') }}</h3>
            </div>
            <div class="modal-body employeeForm">
                <div class="row">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('messages.employees.documents') }}</th>
                                <th scope="col">{{ __('messages.employees.files') }}</th>
                                <th scope="col">{{ __('messages.employees.expiry_date') }}</th>
                                <th scope="col">{{ __('messages.common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($customer))
                                @foreach ($customer?->documents as $document)
                                    <tr>
                                        <td>{{ $document->name }}</td>
                                        <td>{{ $document->file }}</td>
                                        <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ asset('uploads/public/customer_docs/' . basename($document->file)) }}"
                                                class="btn btn-outline-primary" target="_blank" title="View PDF">
                                                <i class="fas fa-eye"></i>

                                            </a>
                                            <button type="button" class="btn btn-outline-danger fileDeleteBtn"
                                                data-toggle="modal" data-id="{{ $document->id }}"
                                                title="Delete Document">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif


                        </tbody>
                    </table>

                </div>
            </div>

            <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">

                <div class="row">
                    <div class="col">
                        <h3>{{ __('messages.employees.documents') }}</h3>
                    </div>

                    <div class="col">
                        <button type="button" id="addField" class="btn btn-primary mb-3 float-right"><i
                                class="fa fa-plus" aria-hidden="true"></i></button>
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
                        <label for="expiry_date"
                            class="form-label">{{ __('messages.employees.expiry_date') }}</label>
                        <input type="date" name="expiry_date[]" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeField"
                            style="display:none;">Remove</button>
                    </div>
                </div>
            </div>

        </div>


    </div>

    <div class="row" style="float:right;">
        <div class="form-group col-sm-12">
            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        </div>
    </div>
</div>
