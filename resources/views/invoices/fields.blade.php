

<div class="card-body">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
    <div class="row">
        <div class="form-group  col-md-2 col-sm-12">
            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
            {{ Form::select('branch_id', $usersBranches ?? [], $invoice->branch_id ?? null, [
                'class' => 'form-control',
                'required',
                'id' => 'branchSelect',
                'placeholder' => 'Select Branch',
                'style' => "pointer-events: none;
                                                                                        background-color: #e9ecef;",
            ]) }}
        </div>
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('title', __('messages.invoice.title') ) }} <span class="required">*</span>
            {{ Form::text('title', isset($invoice->title) ? $invoice->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus']) }}
        </div> --}}
        <input type="hidden" name="title" value=" ">
        <div class="form-group col-lg-2 col-md-6 col-sm-12">
            {{ Form::label('invoice_no', __('messages.invoice.invoice_number')) }}<span class="required">*</span>
            {{ Form::text('invoice_number', $nextNumber, ['class' => 'form-control', 'required', 'id' => 'invoiceNumber', 'readonly', 'placeholder' => __('messages.invoice.invoice_number')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.customer')) }} <span class="required">*</span>
            {{ Form::select('customer_id', $data['customers'], isset($customerId) ? $customerId : null, ['class' => 'form-control', 'required', 'id' => 'customerSelectBox', 'placeholder' => __('messages.placeholder.select_customer')]) }}
        </div>
        
        @if(Auth::user()->is_admin == 1)
        
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('invoice_date', __('messages.invoice.invoice_date')) }} <span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('invoice_date', isset($invoice->invoice_date) ? $invoice->invoice_date : null, ['class' => 'form-control invoiceDate', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.invoice_date')]) }}
            </div>
        </div>
        
        @else 
        
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('invoice_date', __('messages.invoice.invoice_date')) }} <span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('invoice_date', isset($invoice->invoice_date) ? $invoice->invoice_date : date('Y-m-d'), ['class' => 'form-control ', 'required', 'readonly', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.invoice_date')]) }}
            </div>
        </div>
        
        @endif
        
        
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('due_date', 'Invoice Month') }}
            {{ Form::month('invoice_month', null, ['class' => 'form-control ', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('due_date', __('messages.invoice.due_date')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('due_date', isset($invoice->due_date) ? $invoice->due_date : null, ['class' => 'form-control invoiceDueDate', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.due_date')]) }}
            </div>
        </div>


        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('tag', __('messages.tags') ) }}
            <div class="input-group">
                {{ Form::select('tags[]', $data['tags'], null, ['class' => 'form-control', 'id' => 'tagId', 'autocomplete' => 'off', 'multiple' => 'multiple']) }}
                <div class="input-group-append plus-icon-height">
                    <div class="input-group-text">
                        <a href="#" data-toggle="modal" data-target="#addCommonTagModal"><i
                                class="fa fa-plus"></i></a>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('payment_modes', __('messages.invoice.allowed_payment_modes_for_this_invoice')) }}
            <span class="required">*</span>
            <div class="input-group">
                {{ Form::select('payment_modes[]', $data['paymentModes'], null, ['class' => 'form-control', 'id' => 'paymentMode', 'autocomplete' => 'off', 'multiple' => 'multiple', 'required']) }}
                <div class="input-group-append plus-icon-height">
                    <div class="input-group-text">
                        <a href="#" data-toggle="modal" data-target="#addCommonPaymentModeModal"><i
                                class="fa fa-plus"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('currency', __('messages.customer.currency')) }}<span class="required">*</span>
            <select id="invoiceCurrencyId" data-show-content="true" class="form-control currency-select-box"
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
        </div>
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('sale_agent', __('messages.invoice.sale_agent') ) }}
            {{ Form::select('sales_agent_id', $data['saleAgents'], isset($invoice->sales_agent_id) ? $invoice->sales_agent_id : null, ['class' => 'form-control', 'id' => 'salesAgentId', 'placeholder' => __('messages.placeholder.select_sale_agent')]) }}
        </div> --}}
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') ) }}<span
                class="required">*</span>
            {{ Form::select('discount_type', $data['discountType'], isset($invoice->discount_type) ? $invoice->discount_type : null, ['class' => 'form-control', 'id' => 'discountTypeSelect', 'placeholder' => __('messages.placeholder.select_document_type'), 'required']) }}
        </div> --}}
        {{-- <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('address', __('messages.invoice.bill_to') ) }}
            {{ Form::text('address', isset($invoice->address) ? $invoice->address : null, ['class' => 'form-control']) }}
        </div> --}}

        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.project')) }}<span class="required">*</span>
            {{ Form::select('project_id', $projects ? $projects->pluck('project_name', 'id') : null, null, ['class' => 'form-control', 'required', 'id' => 'projectSelect', 'placeholder' => __('messages.placeholder.select_project')]) }}
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('customer', __('messages.project.po_number')) }}
            {{ Form::text('po_number', null, ['class' => 'form-control', 'required', 'id' => 'po_number', 'disabled']) }}
        </div>
        <div class="form-group col-md-2 col-sm-12">
            {{ Form::label('vendor_code', __('messages.customer.vendor_code')) }}
            {{ Form::text('vendor_code', $invoice->vendor_code ?? '', ['class' => 'form-control', 'id' => 'vendor_code', 'autocomplete' => 'off', 'readonly']) }}
        </div>

        <div class="form-group col-md-2 col-sm-12">
            <a href="#" data-toggle="modal" data-target="#addModal" class="mr-3 addressModalIcon"><i
                    class="fa fa-edit"></i></a>
            {{ Form::label('bill_to', __('messages.invoice.bill_to')) }}
            <div id="bill_to" class="ml-5">
                _ _ _ _ _ _
            </div>
        </div>
        {{-- <div class="form-group col-lg-2 col-md-6 col-sm-12">
            {{ Form::label('ship_to', __('messages.invoice.ship_to') ) }}
            <div id="ship_to">
                _ _ _ _ _ _
            </div>
        </div> --}}

    </div>
    <br>
    @include('invoices.add_items')
    <hr />
    <br />
    {{-- <div class="row">
        <div class="form-group col-lg-6 col-md-12 col-sm-12">
            {{ Form::label('', 'Services') }}
            <div class="input-group">
                {{ Form::select('item', $data['items'], null, ['class' => 'form-control', 'id' => 'addItemSelectBox', 'placeholder' => __('messages.placeholder.select_product')]) }}
            </div>
        </div>
        <div
            class="form-group col-lg-6 col-md-12 col-sm-12 showQuantityAs d-flex align-items-center justify-content-end">
            <span class="font-weight-bold mr-2">{{ __('messages.invoice.show_quantity_as')  }}</span>
            <div class="float-right showQuantityAs">
                <div class="custom-control custom-radio mr-3 d-inline-block">
                    <input type="radio" id="qty" name="unit" required value="1"
                        class="custom-control-input" data-quantity-for="qty" checked>
                    <label class="custom-control-label" for="qty">{{ __('messages.invoice.qty') }}</label>
                </div>
                <div class="custom-control custom-radio mr-3 d-inline-block">
                    <input type="radio" id="hours" name="unit" required value="2"
                        class="custom-control-input" data-quantity-for="hours">
                    <label class="custom-control-label" for="hours">{{ __('messages.invoice.hours') }}</label>
                </div>
                <div class="custom-control custom-radio d-inline-block">
                    <input type="radio" id="qtyHours" name="unit" required value="3"
                        class="custom-control-input" data-quantity-for="qtyHours">
                    <label class="custom-control-label" for="qtyHours">{{ __('messages.invoice.qty/hours') }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-12 col-md-12 col-sm-12 overflow-section">
            <table class="table table-responsive-sm table-responsive-md table-striped table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.invoice.item') }}<span class="required">*</span></th>
                        <th>{{ __('messages.common.description') }}</th>
                        <th class="small-column"><span class="qtyHeader">{{ __('messages.invoice.qty') }}</span><span
                                class="required">*</span></th>
                        <th class="small-column">{{ __('messages.products.rate') }}
                            <span class="required">*</span>
                        </th>
                        <th class="medium-column">{{ __('messages.products.tax') }}(<i
                                class="fas fa-percentage"></i>)</th>
                        <th class="small-column">{{ __('messages.invoice.amount') }}<span class="required">*</span>
                        </th>
                        <th class="button-column"><a href="#" id="itemAddBtn"></a>
                        </th>
                    </tr>
                </thead>
                <tbody class="items-container">
                    <tr>
                        <td><input type="text" name="item[]" id="item" class="form-control item-name"
                                required placeholder="{{ __('messages.invoice.item') }}"></td>
                        <td>
                            <textarea name="description[]" class="form-control item-description"
                                placeholder="{{ __('messages.common.description') }}"></textarea>
                        </td>
                        <td><input type="text" name="quantity[]" id="quantity" class="form-control qty" required
                                min="0" placeholder="{{ __('messages.invoice.qty') }}">
                        </td>
                        <td><input type="text" name="rate[]" id="rate" class="form-control rate" required
                                placeholder="{{ __('messages.products.rate') }}"></td>
                        <td class="">
                            <select name="" class="form-control tax-rates" readonly >
                            </select>
                        </td>
                        <td class="item-amount-width"><span
                                class="item-amount">0</span></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-2 col-md-6 col-sm-12">
            {{ Form::label('sub_total', __('messages.invoice.sub_total') ) }}
            <p><span class="footer-numbers sub-total" id="subTotal">0</span>
            </p>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="fDiscount form-group">
                {{ Form::label('discount', __('messages.invoice.discount') ) }}
                (<span class="footer-discount-numbers">0</span>)
                <div class="input-group">
                    {{ Form::text('final_discount', 0, ['class' => 'form-control footer-discount-input', 'placeholder' => __('messages.invoice.discount')]) }}
                    <div class="input-group-append">
                        <select class="input-group-text dropdown" id="footerDiscount" name="discount_symbol">
                            <div class="dropdown-menu">
                                <option value="1" class="dropdown-item">%</option>
                                <option value="2" class="dropdown-item" selected>{{ __('messages.invoice.fixed') }}
                                </option>
                            </div>
                        </select>
                    </div>
                </div>
            </div>
            <table id="taxesListTable" class="w-100">
            </table>
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('adjustment', __('messages.invoice.adjustment') ) }}
            ( <span class="adjustment-numbers">0</span>)
            {{ Form::number('adjustment', 0, ['class' => 'form-control', 'id' => 'adjustment', 'placeholder' => __('messages.invoice.adjustment')]) }}
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('total', __('messages.invoice.total') ) }}
            <p> <span class="total-numbers"> 0</span></p>
        </div>
    </div> --}}


    <div class="row float-right">
        {{-- <div class="form-group col-lg-12 col-md-12 col-sm-12">
            {{ Form::label('admin_note', __('messages.invoice.admin_note') ) }}
            {{ Form::textarea('admin_text', isset($settings) ? $settings['admin_note'] : null, ['class' => 'form-control summernote-simple']) }}
        </div>
        <div class="form-group col-lg-12 col-md-12 col-sm-12">
            {{ Form::label('client_note', __('messages.invoice.client_note') ) }}
            {{ Form::textarea('client_note', isset($settings) ? $settings['client_note'] : null, ['class' => 'form-control summernote-simple']) }}
        </div> --}}
        {{-- <div class="form-group col-lg-12 col-md-12 col-sm-12">
            {{ Form::label('terms_conditions', __('messages.invoice.terms_conditions') ) }}
            {{ Form::textarea('term_conditions', isset($settings) ? $settings['term_and_conditions'] : null, ['class' => 'form-control summernote-simple']) }}
        </div> --}}
        <div class="form-group col-md-2 ">
            <div class="btn-group dropup open">

                {{-- <a href="{{ url()->previous() }}"
                    class="btn btnSecondary text-white mr-2">{{ __('messages.common.cancel') }}</a> --}}
                {{ Form::button('Submit', ['class' => 'btn btn-primary', 'style' => 'line-height:31px;']) }}
                {{-- <a href="#" class="btn btn-primary" id="saveAsDraft"
                    data-status="0">{{ __('messages.common.save') }}</a> --}}
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-left width200">
                    <li>
                        <a href="#" class="dropdown-item" id="saveAsDraft"
                            data-status="0">{{ __('messages.invoice.save_as_draft') }}</a>
                    </li>
                    <li>
                        <a href="#" class="dropdown-item" id="saveAndSend"
                            data-status="1">{{ __('messages.invoice.save_and_send') }}</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
