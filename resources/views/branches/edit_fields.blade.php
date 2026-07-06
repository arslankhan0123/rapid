<div class="tab-content" id="myTabContent2">
    <div class="tab-pane fade show active" id="cForm" role="tabpanel" aria-labelledby="customerForm">
        <div class="row">

            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('company_name', __('messages.assets.company') . ':') }}<span class="required">*</span>
                {{ Form::select('company_name', [$company->value => $company->value], $company->value, ['class' => 'form-control', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">

                {{ Form::label('company_name', __('messages.branches.branch_name') . ':') }}<span
                    class="required">*</span>
                {{ Form::text('name', $branch->name, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus' => true]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('vat_number', __('messages.customer.vat_number') . ':') }}
                {{ Form::text('vat_number', $branch->vat_number, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('website', __('messages.customer.website') . ':') }}
                {{ Form::text('website', $branch->website, ['class' => 'form-control', 'id' => 'website', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('phone', __('messages.customer.phone') . ':') }}<br>
                {{ Form::tel('phone', $branch->phone, ['class' => 'form-control', 'id' => 'phoneNumber', 'placeholder' => __('messages.customer.phone')]) }}
                {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
                <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
                <span id="error-msg" class="hide"></span>
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('currency', __('messages.customer.currency') . ':') }}
                {{ Form::select('currency_id', $currencies, $branch->currency_id, ['class' => 'form-control', 'id' => 'select_country', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('country', __('messages.customer.country') . ':') }}
                {{ Form::select('country_id', $countries, $branch->country_id, ['id' => 'countryId', 'class' => 'form-control']) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('city', __('messages.customer.city') . ':') }}
                {{ Form::text('city', $branch->city, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off']) }}
            </div>
            {{-- billing address --}}
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('street', __('messages.customer.state') . ':') }}
                {{ Form::text('state', $branch->state, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off']) }}
            </div>

            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('zip', __('messages.customer.zip_code') . ':') }}
                {{ Form::text('zip_code', $branch->zip_code, ['class' => 'form-control', 'id' => 'billingZip', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off', 'placeholder' => __('messages.customer.zip_code')]) }}
            </div>
            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('currency', __('messages.banks.name') . ':') }}
                {{ Form::select('bank_id', $banks ?? [], $branch->bank_id, ['class' => 'form-control select2', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group d-none col-md-6 col-sm-12">
                {{ Form::label('currency', 'Print Format') }}
                {{ Form::select('print_format', $printFormats, $branch->print_format, ['class' => 'form-control', 'id' => 'select_print_format', 'autocomplete' => 'off']) }}
            </div>
            <div class="col-lg-6 col-6">
                <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                <div class="form-group">
                    {{ Form::label('title', __('messages.customer.address') . ':') }}
                    {{ Form::textarea('address', $branch->address, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    {{ Form::label('address_ar', __('messages.customer.address') . ' (Arabic):') }}
                    {{ Form::textarea('address_ar', $branch->address_ar, [
                        'class' => 'form-control summernote-simple',
                        'id' => 'address_ar',
                        'direction' => 'rtl', // Right-to-left direction
                    ]) }}
                </div>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                {{ Form::label('invoice_format', __('Invoice Format') . ':') }}
                {{ Form::select('invoice_format', $invoiceFormats, $branch->invoice_format, [
                    'class' => 'form-control select2',
                    'id' => 'select_invoice_format',
                    'autocomplete' => 'off',
                ]) }}
            </div>



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
                            @foreach ($branch->documents as $document)
                                <tr>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ $document->file }}</td>
                                    <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ asset('uploads/public/branch_docs/' . basename($document->file)) }}"
                                            class="btn btn-primary" target="_blank" title="View PDF">
                                            <i class="fas fa-eye"></i>

                                        </a>
                                        <button type="button" class="btn btn-danger fileDeleteBtn" data-toggle="modal"
                                            data-id="{{ $document->id }}" title="Delete Document">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
            <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                <div class="row">
                    <div class="col">
                        <h3>{{ __('messages.employees.upload_documents') }}</h3>
                    </div>

                    <div class="col">
                        <button type="button" id="addField" class="btn btn-primary mb-3 float-right"><i
                                class="fa fa-plus" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-body employeeForm">
                <div id="formContainer" class="mb-3">
                    <div class="form-group row mb-3">
                        <div class="col-md-4">
                            <label for="doc_name" class="form-label">Name:</label>
                            <input type="text" name="doc_name[]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }},
                                {{ __('messages.employees.max_size') }}</label>
                            <input type="file" name="file[]" class="form-control" accept="application/pdf">
                        </div>
                        <div class="col-md-3">
                            <label for="expiry_date" class="form-label">Expiry Date:</label>
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
        <div class="form-group " style="float:right;">
            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        </div>
    </div>

</div>
