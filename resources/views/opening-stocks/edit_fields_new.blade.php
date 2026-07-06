<div class="card-body pt-1">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
    <div class="row  ">
        {{ Form::hidden('title', isset($estimate->title) ? $estimate->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.products.title')]) }}
        <div class="form-group  col-md-2 col-sm-6 ">

            {{ Form::label('estimate_number', 'Document Number') }} <a href="#"
                class="mr-3 btnDocumentNumber float-right" style="font-weight:bolder;">...</a>
            <input type="text"
                value="{{ isset($estimate->estimate_number) ? $estimate->estimate_number : rand(5000, 10000) }}"
                class="form-control" readonly name="estimate_number">
        </div>


        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'P.O Number') }}
            {{ Form::text('po_number', $estimate->po_number ?? null, ['class' => 'form-control', 'required', 'id' => 'estimateNumber']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_date', 'Document Date') }} <span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="height:38px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_date', $estimate->estimate_date ?? null, ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>


        <div class="form-group col-md-6 col-sm-12 d-none">
            <label for="customer">Supplier Name</label><span class="required">*</span>
            <input type="text" id="supplierAutocomplete" class="form-control" name="customer_name"
                value="{{ $estimate->customer_name }}">
            {{ Form::hidden('customer_id', $estimate->customer_id, ['id' => 'customerId']) }}
        </div>
        <div class="form-group col-md-2 col-sm-12  d-none">
            {{ Form::label('estimate_number', 'Payment Mode') }}
            {{ Form::select('payment_mode_id', $paymentModes ?? [], $estimate->payment_mode_id, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-12 d-none">
            {{ Form::label('expiry_date', 'Due Days') }}
            {{ Form::select('due_days', ['30 days' => '30 days', '45 days' => '45 days', '60 days' => '60 days', '90 days' => '90 days', '120 days' => '120 days'],  $estimate->due_days, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'Payment Date') }}
            <input type="text" class="form-control" disabled>
        </div>
        <div class="form-group col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'Supplier Number') }}
            <input type="text" class="form-control" disabled id="supplierNumber"
                value="{{ $estimate->customer_id }}">
        </div>
        <div class="form-group  col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'Supplier Status') }}
            <input type="text" class="form-control supplierStatus" disabled value="Active">
        </div>
        <div class="form-group  col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'Supplier Group') }}
            <input type="text" class="form-control supplierGroups" disabled
                value="{{ $customer->supplierGroups ?? '' }}">
        </div>

        <div class="form-group col-md-6 col-sm-12">
            {{ Form::label('client_note', 'Remarks') }}
            {{ Form::text('client_note', $estimate->client_note ?? null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'Country') }}
            <input type="text" class="form-control country" disabled
                value="{{ $customer->supplierCountry->name ?? '' }}">
        </div>
        <div class="form-group  col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'State') }}
            <input type="text" class="form-control state" disabled value="{{ $customer->supplierState->name ?? '' }}">
        </div>
        <div class="form-group  col-md-2 col-sm-6 d-none">
            {{ Form::label('estimate_number', 'City') }}<a href="#" class="mr-3 btnAddress float-right"
                style="font-weight: bolder;">...</a>
            <input type="text" class="form-control city" disabled value="{{ $customer->city ?? '' }}">
        </div>

    </div>

    @include('opening-stocks.edit_terms_modal')
    @include('opening-stocks.edit_address_modal')
    @include('opening-stocks.edit_document_modal')
    @include('opening-stocks.createItem')
    @include('opening-stocks.edit_items_new')



    <div class="row ">
        <div class="col d-none">
            <a class="btn btnWarning text-white bg-primary form-btn" id="btnTermsNconditons">Terms & Condition</a>
        </div>
        <div class="col  d-flex justify-content-end">
            <div class="btn-group dropup ">
                <a href="{{ route('opening-stocks.create') }}"
                    class="btn btnWarning text-white form-btn mr-2">Reset</a>
                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'id' => 'btnSave', 'class' => 'btn btn-primary form-btn', 'style' => 'line-height:31px;', 'disabled']) }}
            </div>
        </div>
    </div>

</div>
