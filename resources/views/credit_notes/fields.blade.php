<div class="card-body">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
    <div class="row">
        <div class="form-group  col-md-4 col-sm-12">
            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
            {{ Form::select('branch_id', $usersBranches ?? [], null, [
                'class' => 'form-control',
                'id' => 'inputBranch',
                'required',
                'style' => " pointer-events: none;
                                        background-color: #e9ecef;",
            ]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('title', __('messages.credit_note.title') . ':') }}<span class="required">*</span>
            {{ Form::text('title', isset($creditNote->title) ? $creditNote->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus']) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('title', __('messages.credit_note.invoice_id') . ':') }}<span class="required">*</span>
            {{ Form::text('invoice_id', $creditNote->invoice_id ?? null, ['class' => 'form-control', 'required', 'id' => 'invoice_id', 'autocomplete' => 'off', 'autofocus']) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('credit_note_number', __('messages.credit_note.credit_note_number') . ':') }}<span
                class="required">*</span>
            {{ Form::text('credit_note_number', $nextNumber ?? null, ['class' => 'form-control', 'required', 'id' => 'creditNoteNumber', 'readonly']) }}


        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.customer') . ':') }}<span class="required">*</span>
            {{ Form::select('customer_id', $data['customers'], isset($customerId) ? $customerId : null, ['class' => 'form-control', 'required', 'id' => 'customerSelectBox', 'disabled' => 'true']) }}
            <input type="hidden" class="form-control" name="customer_id" id="customerID">
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
                {{ Form::text('credit_note_date', isset($creditNote->credit_note_date) ? $creditNote->credit_note_date : null, ['class' => 'form-control credit_noteDate', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('currency', __('messages.customer.currency') . ':') }}<span class="required">*</span>
            <select id="creditNoteCurrencyId" data-show-content="true" class="form-control currency-select-box" disabled
                name="currency" required>

                @foreach ($data['currencies'] as $key => $currency)
                    <option value="{{ $key }}"
                        {{ $key == getCurrentCurrencyIndex(getCurrentCurrency()) ? 'selected' : '' }}>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="currency" id="currencyInput">
        </div>
        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') . ':') }}<span
                class="required">*</span>
            {{ Form::select('discount_type', $data['discountType'], isset($creditNote->discount_type) ? $creditNote->discount_type : null, ['class' => 'form-control', 'required', 'id' => 'discountTypeSelect', 'placeholder' => __('messages.placeholder.select_discount_type')]) }}
        </div> --}}
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('reference', __('messages.credit_note.reference') . ':') }}
            {{ Form::text('reference', isset($creditNote->reference) ? $creditNote->reference : null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.project') . ':') }}
            {{ Form::text('project_id', null, ['class' => 'form-control', 'required', 'id' => 'projectSelectBox', 'disabled']) }}
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12">
            {{ Form::label('customer', __('messages.project.po_number') . ':') }}
            {{ Form::text('po_number', null, ['class' => 'form-control', 'required', 'id' => 'po_number', 'disabled']) }}
        </div>
        <div class="form-group col-md-4 col-sm-12">
            {{ Form::label('vendor_code', __('messages.customer.vendor_code')) }}
            {{ Form::text('vendor_code', $creditNote->vendor_code ?? '', ['class' => 'form-control', 'id' => 'vendor_code', 'autocomplete' => 'off', 'readonly']) }}
        </div>

        <div class="form-group  col-md-4 col-sm-12">
            <a href="#" {{-- data-toggle="modal" --}} data-target="#addModal" class="mr-3 addressModalIcon"><i
                    class="fa fa-edit"></i></a>
            {{ Form::label('bill_to', __('messages.invoice.bill_to') . ':') }}
            <div id="bill_to" class="ml-5">
                _ _ _ _ _ _
            </div>
        </div>
        {{-- <div class="form-group col-lg-2 col-md-4 col-sm-12">
            {{ Form::label('ship_to', __('messages.invoice.ship_to') . ':') }}
            <div id="ship_to">
                _ _ _ _ _ _
            </div>
        </div> --}}
        {{--
        --}}
        <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('admin_note', 'Remarks') }}
            {{ Form::textarea('admin_text', null, ['class' => 'form-control', 'rows' => 2, 'style' => 'height:auto']) }}

        </div>
    </div>
    @include('credit_notes.add_items')
    {{-- <hr>
    <br>
    <div class="row">
        <div class="form-group col-lg-6 col-md-12 col-sm-12">
            {{ Form::label('', 'Services' . ':') }}
            <div class="input-group">
                {{ Form::select('item', $data['items'], null, ['class' => 'form-control', 'id' => 'addItemSelectBox', 'placeholder' => __('messages.placeholder.select_product')]) }}
            </div>
        </div>
        <div
            class="form-group col-lg-6 col-md-12 col-sm-12 showQuantityAs d-flex align-items-center justify-content-end">
            <span class="font-weight-bold mr-2">{{ __('messages.invoice.show_quantity_as') . ':' }}</span>
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
                        <th class="small-column">{{ __('messages.products.rate') }}()<span class="required">*</span></th>
                        <th class="medium-column">{{ __('messages.products.tax') }}(<i class="fas fa-percentage"></i>)
                        </th>
                        <th class="small-column">{{ __('messages.invoice.amount') }}<span class="required">*</span>
                        </th>
                        <th class="button-column"><a href="#" id="itemAddBtn"></a></th>
                    </tr>
                </thead>
                <tbody class="items-container">
                    <tr>
                        <th><input type="text" name="item[]" class="form-control item-name" required
                                placeholder="{{ __('messages.invoice.item') }}"></th>
                        <td>
                            <textarea name="description[]" class="form-control item-description"
                                placeholder="{{ __('messages.common.description') }}"></textarea>
                        </td>
                        <td><input type="text" name="quantity[]" class="form-control qty" required min="0"
                                placeholder="{{ __('messages.invoice.qty') }}"></td>
                        <td><input type="text" name="rate[]" class="form-control rate" required
                                placeholder="{{ __('messages.products.rate') }}"></td>
                        <td class="">
                            <select name="" class="form-control tax-rates" >
                            </select>
                        </td>
                        <td> <span class="item-amount">0</span></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-2 col-md-6 col-sm-12">
            {{ Form::label('sub_total', __('messages.invoice.sub_total') . ':') }}
            <p> <span class="footer-numbers sub-total" id="subTotal">0</span>
            </p>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="fDiscount form-group">
                {{ Form::label('discount', __('messages.invoice.discount') . ':') }}
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
            {{ Form::label('adjustment', __('messages.invoice.adjustment') . ':') }}
            (<span class="adjustment-numbers">0</span>)
            {{ Form::number('adjustment', 0, ['class' => 'form-control', 'id' => 'adjustment', 'autocomplete' => 'off', 'placeholder' => __('messages.invoice.adjustment')]) }}
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('total', __('messages.invoice.total') . ':') }}
            <p> <span class="total-numbers">0</span></p>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('client_note', __('messages.invoice.client_note') . ':') }}
            {{ Form::textarea('client_note', isset($settings) ? $settings['client_note'] : null, ['class' => 'form-control summernote-simple']) }}
        </div>
        <div class="form-group col-sm-12 col-lg-12 col-md-12">
            {{ Form::label('terms_conditions', __('messages.invoice.terms_conditions') . ':') }}
            {{ Form::textarea('term_conditions', isset($settings) ? $settings['term_and_conditions'] : null, ['class' => 'form-control summernote-simple']) }}
        </div>
    </div> --}}





    <div class="row float-right">
        <div class="form-group col-md-2 ">
            <div class="btn-group dropup open ">
                {{-- <a href="{{ url()->previous() }}"
                    class="btn btnSecondary text-white mr-2">{{ __('messages.common.cancel') }}</a> --}}
                {{--                {{ Form::button('Save', ['class' => 'btn btn-primary']) }} --}}
                <a href="#" class="btn btn-primary" id="saveAsDraft" style="line-height: 30px;"
                    data-status="0">{{ __('messages.common.submit') }}</a>
                <button type="button" class="btn btn-info dropdown-toggle " data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-left width200 ">
                    <li>
                        <a href="#" class="dropdown-item" id="saveAsDraft"
                            data-status="0">{{ __('messages.credit_note.save_as_draft') }}</a>
                    </li>
                    <li>
                        <a href="#" class="dropdown-item" id="saveAndSend"
                            data-status="1">{{ __('messages.credit_note.save_and_send') }}</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
    <div class="row justify-content-end">

    </div>
</div>
