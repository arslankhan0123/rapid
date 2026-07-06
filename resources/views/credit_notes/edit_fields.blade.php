<input type="hidden" id="hdnCreditNoteId" value="{{ $creditNote->id }}">
<div class="card-body">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
    <div class="row">
        <div class="form-group  col-md-4 col-sm-12">
            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
            {{ Form::select('branch_id', $usersBranches ?? [], $creditNote->branch_id ?? null, [
                'class' => 'form-control',
                'id' => 'inputBranch',
                'required',
                'style' => " pointer-events: none;
                                                    background-color: #e9ecef;",
            ]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('title', __('messages.credit_note.title') . ':') }}<span class="required">*</span>
            {{ Form::text('title', isset($creditNote->title) ? $creditNote->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.products.title')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('title', __('messages.credit_note.invoice_id') . ':') }}<span class="required">*</span>
            {{ Form::text('invoice_id', $creditNote->invoice_id ?? null, ['class' => 'form-control', 'required', 'id' => 'invoice_id', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.credit_note.invoice_id')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.customer') . ':') }}<span class="required">*</span>
            {{ Form::select('customer_id', $data['customers'], isset($creditNote->customer_id) ? $creditNote->customer_id : null, ['class' => 'form-control', 'required', 'id' => 'customerSelectBox', 'disabled' => true, 'placeholder' => __('messages.placeholder.select_customer')]) }}

            <input type="hidden" class="form-control" name="customer_id" id="customerID"
                value="{{ $creditNote->customer_id }}">

        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('invoice_no', __('messages.credit_note.credit_note_number') . ':') }}<span
                class="required">*</span>

            {{ Form::text('credit_note_number', $creditNote->credit_note_number ?? rand(100000, 9999999), ['class' => 'form-control', 'required', 'id' => 'creditNoteNumber', 'placeholder' => __('messages.credit_note.credit_note_number')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('credit_note_date', __('messages.credit_note.credit_note_date') . ':') }} <span
                class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('credit_note_date', isset($creditNote->credit_note_date) ? date('Y-m-d H:i:s', strtotime($creditNote->credit_note_date)) : null, ['class' => 'form-control credit_noteDate', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.credit_note.credit_note_date')]) }}
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('currency', __('messages.customer.currency') . ':') }}<span class="required">*</span>
            <select id="creditNoteCurrencyId" data-show-content="true" class="form-control currency-select-box" disabled
                name="currency" required>

                @foreach ($data['currencies'] as $key => $currency)
                    <option value="{{ $key }}"
                        {{ (isset($creditNote->currency) ? $creditNote->currency : null) == $key ? 'selected' : '' }}>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="currency" id="currencyInput" value="{{ $creditNote->currency }}">

        </div>
        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') . ':') }}<span
                class="required">*</span>
            {{ Form::select('discount_type', $data['discountType'], isset($creditNote->discount_type) ? $creditNote->discount_type : null, ['class' => 'form-control', 'required', 'id' => 'discountTypeSelect', 'placeholder' => __('messages.placeholder.select_discount_type')]) }}
        </div> --}}

        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('reference', __('messages.credit_note.reference') . ':') }}
            {{ Form::text('reference', isset($creditNote->reference) ? $creditNote->reference : null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.credit_note.reference')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.project') . ':') }}
            {{ Form::text('project_id', $creditNote->invoice->project->project_name ?? null, ['class' => 'form-control', 'required', 'id' => 'projectSelectBox', 'disabled']) }}
        </div>

        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.project.po_number') . ':') }}
            {{ Form::text('po_number', $creditNote->invoice->project->po_number ?? null, ['class' => 'form-control', 'required', 'id' => 'po_number', 'disabled']) }}
        </div>
        <div class="form-group col-md-4 col-sm-12">
            {{ Form::label('vendor_code', __('messages.customer.vendor_code')) }}
            {{ Form::text('vendor_code', $creditNote->vendor_code ?? '', ['class' => 'form-control', 'id' => 'vendor_code', 'autocomplete' => 'off', 'readonly']) }}
        </div>

        <div class="form-group col-md-4 col-sm-12">
            <a href="#" {{-- data-toggle="modal" data-target="#addModal" --}} class="mr-3 addressModalIcon"><i class="fa fa-edit"></i></a>
            {{ Form::label('bill_to', __('messages.invoice.bill_to') . ':') }}
            <div id="bill_to">
                _ _ _ _ _ _
            </div>
        </div>
        {{-- <div class="form-group col-lg-2 col-md-4 col-sm-12">
            {{ Form::label('ship_to', __('messages.invoice.ship_to') . ':') }}
            <div id="ship_to">
                _ _ _ _ _ _
            </div>
        </div> --}}
        {{-- <div class="form-group col-lg-12 col-sm-12 col-md-12">
            {{ Form::label('admin_note', __('messages.invoice.admin_note') . ':') }}
            {{ Form::textarea('admin_text', isset($creditNote->admin_text) ? nl2br(e($creditNote->admin_text)) : null, ['class' => 'form-control summernote-simple', 'id' => 'editAdminNote']) }}
        </div> --}}
        {{-- <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('admin_note', 'Remarks') }}
            {{ Form::textarea('admin_text', isset($settings) ? $settings['admin_note'] : null, ['class' => 'form-control summernote-simple']) }}
        </div> --}}
        <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('admin_note', 'Remarks') }}
            {{ Form::textarea('admin_text', $creditNote->admin_text ?? '', ['class' => 'form-control', 'rows' => 2, 'style' => 'height:auto']) }}
        </div>
    </div>

    @include('credit_notes.edit_items')
    {{-- <hr>
    <br>
    <div class="row">
        <div class="form-group col-lg-6 col-md-12 col-sm-12">
            {{ Form::label('', 'Services' . ':') }}
            <div class="input-group">
                {{ Form::select('item', $data['items'], null, ['class' => 'form-control', 'id' => 'addItemSelectBox', 'placeholder' => __('messages.placeholder.select_product')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 showQuantityAs d-flex align-items-center justify-content-end">
            <span class="font-weight-bold mr-2">{{ __('messages.invoice.show_quantity_as') . ':' }}</span>
            <div class="float-right showQuantityAs">
                <div class="custom-control custom-radio mr-3 d-inline-block">
                    <input type="radio" id="qty" name="unit" required value="1"
                        class="custom-control-input" data-quantity-for="qty"
                        {{ $creditNote->unit == 1 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="qty">{{ __('messages.invoice.qty') }}</label>
                </div>
                <div class="custom-control custom-radio mr-3 d-inline-block">
                    <input type="radio" id="hours" name="unit" required value="2"
                        class="custom-control-input" data-quantity-for="hours"
                        {{ $creditNote->unit == 2 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="hours">{{ __('messages.invoice.hours') }}</label>
                </div>
                <div class="custom-control custom-radio d-inline-block">
                    <input type="radio" id="qtyHours" name="unit" required value="3"
                        class="custom-control-input" data-quantity-for="qtyHours"
                        {{ $creditNote->unit == 3 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="qtyHours">{{ __('messages.invoice.qty_hours') }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-12 col-md-12 col-sm-12 overflow-section">
            <table class="table table-responsive-sm table-responsive-md table-striped table-bordered" id="itemTable">
                <thead>
                    <tr>
                        <th>{{ __('messages.invoice.item') }}<span class="required">*</span></th>
                        <th>{{ __('messages.common.description') }}</th>
                        <th class="small-column"><span class="qtyHeader">{{ __('messages.invoice.qty') }}</span><span
                                class="required">*</span></th>
                        <th class="small-column">{{ __('messages.products.rate') }}(<i
                                data-set-currency-class="true"></i>)<span class="required">*</span></th>
                        <th class="medium-column">{{ __('messages.products.tax') }}(<i class="fas fa-percentage"></i>)
                        </th>
                        <th class="small-column">{{ __('messages.invoice.amount') }}<span class="required">*</span>
                        </th>
                        <th class="button-column"><a href="#" id="itemAddBtn"></a></th>
                    </tr>
                </thead>
                <tbody class="items-container">
                    @foreach ($creditNote->salesItems as $item)
                        <tr>
                            <th><input type="text" name="item[]" class="form-control item-name" required
                                    value="{{ html_entity_decode($item->item) }}"
                                    placeholder="{{ __('messages.invoice.item') }}"></th>
                            <td>
                                <textarea name="description[]" class="form-control item-description"
                                    placeholder="{{ __('messages.common.description') }}">{!! nl2br(e($item->description)) !!}</textarea>
                            </td>
                            <td><input type="text" name="quantity[]" class="form-control qty" required
                                    min="0" value="{{ $item->quantity }}"
                                    placeholder="{{ __('messages.invoice.qty') }}"></td>
                            <td><input type="text" name="rate[]" class="form-control rate" required
                                    value="{{ $item->rate }}" placeholder="{{ __('messages.products.rate') }}">
                            </td>
                            <td class="">
                                {{ Form::select('tax[]', $data['taxesArr'], $item->taxes->pluck('id'), ['class' => 'form-control tax-rates']) }}
                            </td>
                            <td> <span
                                    class="item-amount">{{ number_format($item->total) }}</span></td>
                            <td><a href="#" class="remove-invoice-item text-danger"><i
                                        class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-2 col-md-6 col-sm-12">
            {{ Form::label('sub_total', __('messages.invoice.sub_total') . ':') }}
            <p> <span class="footer-numbers sub-total"
                    id="subTotal">{{ $creditNote->sub_total }}</span></p>
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            <div class="fDiscount form-group">
                {{ Form::label('discount', __('messages.invoice.discount') . ':') }}
                ( <span
                    class="footer-discount-numbers">{{ formatNumber($creditNote->discount) }}</span>)
                <div class="input-group">
                    {{ Form::text('final_discount', $creditNote->discount, ['class' => 'form-control footer-discount-input', 'placeholder' => __('messages.invoice.discount')]) }}
                    <div class="input-group-append">
                        @if (isset($creditNote->discount_type) && $creditNote->discount_type === 0)
                            <input type="hidden" name="discount_symbol" value="0">
                        @endif
                        <select class="input-group-text dropdown" id="footerDiscount" name="discount_symbol">
                            <div class="dropdown-menu">
                                <option value="1" class="dropdown-item"
                                    {{ isset($creditNote->discount_symbol) && $creditNote->discount_symbol == 1 ? 'selected' : '' }}>
                                    %
                                </option>
                                <option value="2" class="dropdown-item"
                                    {{ isset($creditNote->discount_symbol) && $creditNote->discount_symbol == 2 ? 'selected' : '' }}>
                                    {{ __('messages.invoice.fixed') }}</option>
                            </div>
                        </select>
                    </div>
                </div>
            </div>
            <table id="taxesListTable" class="w-100">
                @foreach ($creditNote->salesTaxes as $tax)
                    <tr>
                        <td colspan="2" class="font-weight-bold tax-value">{{ $tax->tax }}%</td>
                        <td class="footer-numbers footer-tax-numbers">{{ $tax->amount }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('adjustment', __('messages.invoice.adjustment') . ':') }}
            ( <span
                class="adjustment-numbers">{{ number_format($creditNote->adjustment) }}</span>)
            {{ Form::number('adjustment', $creditNote->adjustment, ['class' => 'form-control', 'id' => 'adjustment', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.adjustment')]) }}
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('total', __('messages.invoice.total') . ':') }}
            <p> <span
                    class="total-numbers">{{ number_format($creditNote->total_amount) }}</span></p>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('client_note', __('messages.invoice.client_note') . ':') }}
            {{ Form::textarea('client_note', isset($creditNote->client_note) ? $creditNote->client_note : null, ['class' => 'form-control summernote-simple', 'id' => 'editClientNote']) }}
        </div>
        <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('terms_conditions', __('messages.invoice.terms_conditions') . ':') }}
            {{ Form::textarea('term_conditions', isset($creditNote->term_conditions) ? $creditNote->term_conditions : null, ['class' => 'form-control summernote-simple', 'id' => 'editTermAndConditions']) }}
        </div>
    </div> --}}
    <div class="row float-right">
        <div class="form-group col-lg-12 col-md-12 col-sm-12">
            <div class="btn-group dropup open">
                {{-- <a href="{{ url()->previous() }}"
                    class="btn btnSecondary text-white mr-2">{{ __('messages.common.cancel') }}</a> --}}
                {{ Form::button(__('messages.common.submit'), ['class' => 'btn btn-primary', 'style' => 'line-height:30px;']) }}
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-left width200">
                    @if ($creditNote->payment_status == \App\Models\CreditNote::PAYMENT_STATUS_DRAFT)
                        <li>
                            <a href="#" class="dropdown-item" id="editSaveAsDraft"
                                data-status="0">{{ __('messages.credit_note.save_as_draft') }}</a>
                        </li>
                    @endif
                    <li>
                        <a href="#" class="dropdown-item" id="editSaveAndSend"
                            data-status="1">{{ __('messages.credit_note.save_and_send') }}</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
