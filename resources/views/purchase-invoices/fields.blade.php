<div class="card-body pt-1">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
    <div class="row  ">

        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12"> --}}
        {{-- {{ Form::label('title', __('messages.estimate.title') ) }}<span class="required">*</span> --}}
        {{ Form::hidden('title', ' ', ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus']) }}
        {{-- </div> --}}

        <div class="form-group  col-md-2 col-sm-6 ">

            {{ Form::label('estimate_number', 'Document Number') }} <a href="#"
                class="mr-3 btnDocumentNumber float-right" style="font-weight:bolder;">...</a>
            <input type="text" value="{{ $nextNumber }}" class="form-control" readonly name="estimate_number">
        </div>


        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'P.O Number') }}
            {{ Form::text('po_number', null, ['class' => 'form-control', 'required', 'id' => 'estimateNumber']) }}
        </div>


        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('branch_id', 'Branch') }} <span class="required">*</span>
            {{ Form::select('branch_id', $usersBranches ?? [], null, [
                'class' => 'form-control select2',
                'required',
                'id' => 'branchSelect',
                'placeholder' => 'Select Branch',
            ]) }}
        </div>


        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_date', 'P.O Date') }} <span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="height:38px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_date', null, ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>


        <div class="form-group col-md-6 col-sm-12">
            <label for="customer">Supplier Name</label><span class="required">*</span>
            <input type="text" id="supplierAutocomplete" class="form-control" name="customer_name" required>
            {{ Form::hidden('customer_id', null, ['id' => 'customerId']) }}
            {{-- <div class="input-group">
                <!-- Customer Name Input (70% width) -->
                <input type="text" id="customerNameInput" name="customer_name" class="form-control" required>

                <!-- Customer ID Dropdown (30% width) -->
                <div class="input-group-append">
                    {{ Form::select('customer_id', $data['customers'], null, ['class' => 'form-control', 'style' => 'height:10px;', 'id' => 'supplierSelectBox', 'placeholder' => 'Select Supplier']) }}
                </div>
            </div> --}}
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('estimate_number', 'Payment Mode') }}
            {{ Form::select('payment_mode_id', $paymentModes ?? [], null, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-12">
            {{ Form::label('expiry_date', 'Due Days') }}
            {{ Form::select('due_days', ['30 days' => '30 days', '45 days' => '45 days', '60 days' => '60 days', '90 days' => '90 days', '120 days' => '120 days'], null, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Payment Date') }}
            <input type="text" class="form-control" disabled>
        </div>
        <div class="form-group col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Supplier Number') }}
            <input type="text" class="form-control" disabled id="supplierNumber" name="customer_id">
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Supplier Status') }}
            <input type="text" class="form-control supplierStatus" disabled>
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Supplier Group') }}
            <input type="text" class="form-control supplierGroups" disabled>
        </div>
        {{-- <div class="form-group col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Opening Date') }}
            <input type="text" class="form-control" value="{{ date('Y-m-d') }}" disabled>
        </div>
        <div class="form-group col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Account Status') }}
            <input type="text" class="form-control" disabled>
        </div> --}}

        {{-- <div class="form-group  col-md-2 col-sm-12">
            {{ Form::label('currency', 'Currency Name') }}<span class="required">*</span>
            <select id="estimateCurrencyId" data-show-content="true" class="form-control currency-select-box"
                name="currency" required>
                <option value="0" disabled="true" selected="true">{{ __('messages.placeholder.select_currency') }}
                </option>
                @foreach ($data['currencies'] as $key => $currency)
                    <option value="{{ $key }}"
                        {{ $key == getCurrentCurrencyIndex(getCurrentCurrency()) ? 'selected' : '' }}>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div> --}}






        {{-- <div class="form-group col-md-2 col-sm-6">
            {{ Form::label('reference', __('messages.credit_note.reference') ) }}
            {{ Form::text('reference', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>

        <div class="form-group col-md-2 col-sm-6 ">
            <a href="#" class="mr-3 addressModalIcon"><i class="fa fa-edit"></i></a>
            {{ Form::label('bill_to', __('messages.invoice.bill_to') ) }}
            <div id="bill_to" class="ml-5">
                _ _ _ _ _ _
            </div>
        </div>

        <div class="form-group  col-md-4 col-sm-12">
            {{ Form::label('admin_note', 'Subject' ) }}
            {{ Form::text('admin_note', isset($settings) ? $settings['admin_note'] : null, ['class' => 'form-control']) }}
        </div> --}}
        <div class="form-group col-md-6 col-sm-12">
            {{ Form::label('client_note', 'Remarks') }}
            {{ Form::text('client_note', isset($settings) ? $settings['client_note'] : null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Country') }}
            <input type="text" class="form-control country" disabled>
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'State') }}
            <input type="text" class="form-control state" disabled>
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'City') }}<a href="#" class="mr-3 btnAddress float-right"
                style="font-weight: bolder;">...</a>
            <input type="text" class="form-control city" disabled>
        </div>

    </div>

    @include('purchase-orders.terms_modal')
    @include('purchase-orders.add_address_modal')
    @include('purchase-orders.add_document_modal')
    @include('purchase-orders.createItem')
    @include('purchase-orders.add_items')

    <div class="row">
        <div class="col">
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
                    <div class="col-md-5">
                        <label for="doc_name" class="form-label">{{ __('messages.employees.documents') }}</label>
                        <input type="text" name="doc_name[]" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }},
                            {{ __('messages.employees.max_size') }}</label>
                        <input type="file" name="file[]" class="form-control" accept="application/pdf">
                    </div>
                    <div class="col-md-3  d-none">
                        <label for="expiry_date" class="form-label">{{ __('messages.employees.expiry_date') }}</label>
                        <input type="date" name="expiry_date[]" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeField" style="display:none;">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row ">
        <div class="col">
            <a class="btn btnWarning text-white bg-primary form-btn" id="btnTermsNconditons">Terms & Condition</a>
        </div>
        <div class="col  d-flex justify-content-end">
            <div class="btn-group dropup ">
                <a href="{{ route('purchase-orders.create') }}"
                    class="btn btnWarning text-white form-btn mr-2">Reset</a>
                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'id' => 'btnSave', 'class' => 'btn btn-primary form-btn', 'style' => 'line-height:31px;', 'disabled']) }}
            </div>
        </div>
    </div>

</div>
