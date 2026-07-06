<input type="hidden" id="hdnInvoiceId" value="{{ $invoice->id }}">
<div class="card-body">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>

    <div class="row">
        <div class="form-group  col-md-2 col-sm-12">
            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
            {{ Form::select('branch_id', $usersBranches ?? [], $invoice->branch_id ?? null, [
                'class' => 'form-control select2',
                'required',
                'id' => 'branchSelect',
                'placeholder' => 'Select Branch',
            ]) }}
        </div>
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('title', __('messages.invoice.title') ) }}<span class="required">*</span>
            {{ Form::text('title', isset($invoice->title) ? $invoice->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.invoice.title')]) }}
        </div> --}}
        <input type="hidden" name="title" value=" ">
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('invoice_no', __('messages.invoice.invoice_number')) }}<span class="required">*</span>
            {{ Form::text('invoice_number', $invoice->invoice_number ?? null, ['class' => 'form-control', 'required', 'id' => 'invoiceNumber', 'placeholder' => __('messages.invoice.invoice_number')]) }}
        </div>
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.customer') ) }}<span class="required">*</span>
            {{ Form::select('customer_id', $data['customers'], isset($invoice->customer_id) ? $invoice->customer_id : null, ['class' => 'form-control', 'required', 'id' => 'customerSelectBox', 'placeholder' => __('messages.placeholder.select_customer')]) }}
        </div> --}}

        <div class="form-group col-md-4 col-sm-12">
            <label for="customer">Customer <span class="required">*</span></label>

            <div class="d-flex">
                {{-- <input type="text" id="customerNameInput" name="customer_name" class="form-control"
                    style="width: 60%;" placeholder="Customer name" required
                    value="{{ $invoice->customer_name ?? '' }}"> --}}
                <input type="text" id="customerNameInput" name="customer_name" class="form-control"
                    style="width: 60%;" placeholder="Customer name"
                    value="{{ $invoice->customer->company_name ?? '' }}">

                <div style="width: 40%;overflow:hidden;">
                    {{ Form::select('customer_id', $data['customers'] ?? [], $invoice->customer_id ?? null, [
                        'class' => 'form-control select2',
                        'id' => 'supplierSelectBox',
                        'placeholder' => 'Select Customer',
                    ]) }}
                </div>
            </div>
            <div id="customerValidationError" class="text-danger mt-1" style="display:none; font-size:13px;">
                Customer is required. Please enter a name or select from the list.
            </div>
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('invoice_date', __('messages.invoice.invoice_date')) }} <span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('invoice_date', isset($invoice->invoice_date) ? date('Y-m-d', strtotime($invoice->invoice_date)) : null, ['class' => 'form-control invoiceDate', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.invoice_date')]) }}
            </div>
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('due_date', 'Invoice Month') }}
            {{ Form::month('invoice_month', $invoice->invoice_month ?? null, ['class' => 'form-control ', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('due_date', __('messages.invoice.due_date')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('due_date', isset($invoice->due_date) ? date('Y-m-d', strtotime($invoice->due_date)) : null, ['class' => 'form-control invoiceDueDate', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.due_date')]) }}
            </div>
        </div>

        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('payment_modes', __('messages.invoice.allowed_payment_modes_for_this_invoice')) }}
            <span class="required">*</span>
            <div class="input-group">
                {{-- {{ Form::select('payment_modes[]', $data['paymentModes'], $invoice->paymentModes->pluck('id'), ['class' => 'form-control', 'id' => 'paymentMode', 'required', 'autocomplete' => 'off', 'multiple' => 'multiple']) }} --}}
                <input type="text" readonly class="form-control" value="Main Cash" />


            </div>
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('currency', __('messages.customer.currency')) }}<span class="required">*</span>
            <select id="invoiceCurrencyId" data-show-content="true" class="form-control currency-select-box"
                name="currency" required>
                <option value="0" disabled="true" {{ isset($invoice->currency) ? '' : 'selected' }}>
                    {{ __('messages.placeholder.select_currency') }}
                </option>
                @foreach ($data['currencies'] as $key => $currency)
                    <option value="{{ $key }}"
                        {{ (isset($invoice->currency) ? $invoice->currency : null) == $key ? 'selected' : '' }}>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('customer', __('messages.project.po_number')) }}
            {{ Form::text('po_number', $invoice->po_number ?? null, ['class' => 'form-control', 'required', 'id' => 'po_number']) }}
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('vendor_code', __('messages.customer.vendor_code')) }}
            {{ Form::text('vendor_code', $invoice->vendor_code ?? '', ['class' => 'form-control', 'id' => 'vendor_code', 'autocomplete' => 'off']) }}
        </div>
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('project_id', __('messages.invoice.project')) }}
            {{ Form::select('project_id', $projectsOnly ?? [], $invoice->project_id ?? '', ['class' => 'form-control select2', 'placeholder' => 'Select Project']) }}
        </div> --}}

        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('project_id', __('messages.invoice.project')) }}
            <div class="input-group">
                {{-- Left Text Input for Project Name --}}
                <input type="text" class="form-control" id="project_name" name="project_name" style="width: 60%;"
                    {{ $invoice->project_name ?? '' }} placeholder="Project Name">

                {{-- Right Select Dropdown --}}
                {{ Form::select('project_id', $projectsOnly ?? [], $invoice->project_id ?? '', [
                    'class' => 'form-control select2',
                    'placeholder' => 'Select Project',
                    'id' => 'project_id',
                    'style' => 'width:40%;',
                ]) }}
            </div>
        </div>

        <div class="form-group col-md-4 col-sm-12">
            {{ Form::label('vendor_code', 'Address') }}
            {{ Form::text('address', $invoice->address ?? '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>



        {{-- <div class="form-group col-lg-2 col-md-4 col-sm-12">
            <a href="#" data-toggle="modal" data-target="#addModal" class="mr-3 addressModalIcon"><i
                    class="fa fa-edit"></i></a>
            {{ Form::label('bill_to', __('messages.invoice.bill_to') ) }}
            <div id="bill_to" class="ml-5">
                _ _ _ _ _ _
            </div>
        </div> --}}

    </div>

    <br>
    @include('manual-sales.edit_items')
    <hr />
    <br />



    <div class="row float-right">

        <div class="form-group col-md-2 mr-2">
            <div class="btn-group dropup open mb-3">

                {{ Form::button('Submit', ['class' => 'btn btn-primary', 'id' => 'editMainSubmitBtn', 'style' => 'line-height:31px;']) }}
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-left width200">
                    @if ($invoice->payment_status < 1)
                        <li>
                            <a href="#" class="dropdown-item" id="editSaveAsDraft"
                                data-status="0">{{ __('messages.invoice.save_as_draft') }}</a>
                        </li>
                    @endif
                    <li>
                        <a href="#" class="dropdown-item" id="editSaveSend" data-status="1">Save</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
