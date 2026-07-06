<div class="card-body pt-1">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
    <div class="row  ">
        {{ Form::hidden('title', isset($estimate->title) ? $estimate->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.products.title')]) }}
        <div class="form-group  col-md-2 col-sm-6 ">

            {{ Form::label('estimate_number', 'Document Number') }} <a href="#"
                class="mr-3 btnDocumentNumber float-right" style="font-weight:bolder;">...</a>
            <input type="text"
                value="{{ isset($estimate->estimate_number) ? $estimate->estimate_number : rand(5000, 10000) }}"
                class="form-control" readonly name="return_number">
        </div>

        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Purchase Invoice Number') }}
            {{ Form::text('purchase_invoice_id', $estimate->purchase_invoice_id ?? nul, ['class' => 'form-control', 'required', 'id' => 'purchaseInvoiceNumber']) }}
        </div>


        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'P.O Number') }}
            {{ Form::text('po_number', $estimate->po_number ?? null, ['class' => 'form-control', 'required', 'id' => 'estimateNumber','readonly']) }}
        </div>
<div class="form-group col-md-2 col-sm-6"> 
    {{ Form::label('branch_id', 'Branch') }}
    <select name="branch_id" id="branch_id" class="form-control" required>
        <option value="">Select Branch</option>
        @foreach ($userBranches as $id => $name)
            <option value="{{ $id }}" {{ (isset($estimate->branch_id) && $estimate->branch_id == $id) ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>


        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_date', 'P.O Date') }} <span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="height:38px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_date', isset($estimate->estimate_date) ? \Carbon\Carbon::parse($estimate->estimate_date)->format('d-m-Y') : null, ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>


        <div class="form-group col-md-4 col-sm-12">
            <label for="customer">Supplier Name</label><span class="required">*</span>
            <input type="text" id="supplierAutocomplete" class="form-control" name="customer_name" readonly
                value="{{ $estimate->customer_name }}">
            {{ Form::hidden('customer_id', $estimate->customer_id, ['id' => 'customerId']) }}
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('estimate_number', 'Payment Type') }}
            {{ Form::select('payment_type', ['Test Payment Types', 'Dummy'], null, ['class' => 'form-control select2']) }}
        </div>
        {{-- <div class="form-group  col-md-2 col-sm-12">
            {{ Form::label('expiry_date', 'Due Days') }}
            {{ Form::select('payment_type', ['First Day', 'Second Day'], null, ['class' => 'form-control select2']) }}
        </div> --}}
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('expiry_date', 'Due Days') }}
            {{ Form::select('due_days', [
                '30 days' => '30',
                '45 days' => '45',
                '60 days' => '60',
                '90 days' => '90',
                '120 days' => '120'
            ], null, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Payment Date') }}
            <input type="text" class="form-control" disabled>
        </div>
        <div class="form-group col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Supplier Number') }}
            <input type="text" class="form-control" disabled id="supplierNumber" value="{{$estimate->customer_id,}}">
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Supplier Status') }}
            <input type="text" class="form-control supplierStatus" disabled value="Active">
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Supplier Group') }}
            <input type="text" class="form-control supplierGroups" disabled value="{{$customer->supplierGroups??''}}">
        </div>

        <div class="form-group col-md-6 col-sm-12">
            {{ Form::label('client_note', 'Remarks') }}
            {{ Form::text('client_note', $estimate->client_note?? null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'Country') }}
            <input type="text" class="form-control country" disabled value="{{$customer->supplierCountry->name??''}}">
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'State') }}
            <input type="text" class="form-control state" disabled value="{{$customer->supplierState->name??''}}">
        </div>
        <div class="form-group  col-md-2 col-sm-6">
            {{ Form::label('estimate_number', 'City') }}<a href="#" class="mr-3 btnAddress float-right"
                style="font-weight: bolder;">...</a>
            <input type="text" class="form-control city" disabled value="{{$customer->city??''}}">
        </div>

    </div>

    @include('purchase-orders.edit_terms_modal')
    @include('purchase-orders.edit_address_modal')
    @include('purchase-orders.edit_document_modal')
    @include('purchase-orders.createItem')
    @include('purchase-orders.edit_items_new')
<div class="row">
        <div class="col">
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
                                <th scope="col" class="d-none">{{ __('messages.employees.expiry_date') }}</th>
                                <th scope="col" class="text-right">{{ __('messages.common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estimate->documents as $document)
                                <tr>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ $document->file }}</td>
                                    <td class="d-none">{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ asset('uploads/public/purchase_return_docs/' . basename($document->file)) }}"
                                            class="btn btn-outline-primary" target="_blank" title="View PDF">
                                            <i class="fas fa-eye"></i>

                                        </a>
                                        <button type="button" class="btn btn-outline-danger fileDeleteBtn" data-toggle="modal"
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
                        <div class="col-md-5">
                            <label for="doc_name" class="form-label">Name:</label>
                            <input type="text" name="doc_name[]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }},
                                {{ __('messages.employees.max_size') }}</label>
                            <input type="file" name="file[]" class="form-control" accept="application/pdf">
                        </div>
                        <div class="col-md-3 d-none">
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
