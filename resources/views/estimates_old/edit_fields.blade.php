<input type="hidden" id="estimateId" value="{{ $estimate->id }}">
<div class="card-body">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>

    <div class="row">

        {{ Form::hidden('title', isset($estimate->title) ? $estimate->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.products.title')]) }}

        <div class="form-group col-lg-4 col-md-8 col-sm-12">
            {{ Form::label('customer_name', __('messages.estimate.customer_name') . ':') }}<span
                class="required">*</span>
            {{ Form::text('customer_name', $estimate->customer_name ?? '', ['class' => 'form-control', 'requierd', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('reference', __('messages.estimate.reference') . ':') }}
            {{ Form::text('reference', isset($estimate->reference) ? $estimate->reference : null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.credit_note.reference')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('estimate_number', __('messages.estimate.estimate_number') . ':') }}<span
                class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('messages.estimate.estimate_prefix') }}
                    </div>
                </div>
                {{ Form::text('estimate_number', isset($estimate->estimate_number) ? $estimate->estimate_number : generateUniqueEstimateNumber(), ['class' => 'form-control', 'required', 'id' => 'estimateNumber', 'placeholder' => __('messages.estimate.estimate_number')]) }}
            </div>
        </div>
        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('customer', __('messages.invoice.customer') . ':') }}<span class="required">*</span>
            {{ Form::select('customer_id', $data['customers'], isset($estimate->customer_id) ? $estimate->customer_id : null, ['class' => 'form-control', 'required', 'id' => 'customerSelectBox', 'placeholder' => __('messages.placeholder.select_customer')]) }}
        </div> --}}
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('estimate_date', __('messages.estimate.estimate_date') . ':') }} <span
                class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_date', isset($estimate->estimate_date) ? date('Y-m-d H:i:s', strtotime($estimate->estimate_date)) : null, ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.estimate.estimate_date')]) }}
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-8 col-sm-12">
            {{ Form::label('address', __('messages.estimate.mobile') . ':') }}
            {{ Form::text('mobile', $estimate->mobile ?? '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('address', __('messages.company.address') . ':') }}
            {{ Form::text('address', $estimate->address ?? null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.company.address')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-8 col-sm-12">
            {{ Form::label('address', __('messages.estimate.email') . ':') }}
            {{ Form::email('email', $estimate->email ?? '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('expiry_date', __('messages.estimate.expiry_date') . ':') }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_expiry_date', isset($estimate->estimate_expiry_date) ? date('Y-m-d H:i:s', strtotime($estimate->estimate_expiry_date)) : null, ['class' => 'form-control due-datepicker', 'autocomplete' => 'off', 'placeholder' => __('messages.estimate.expiry_date')]) }}
            </div>
        </div>
        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('tag', __('messages.tags') . ':') }}
            <div class="input-group">
                {{ Form::select('tags[]', $data['tags'], isset($estimate->tags) ? $estimate->tags : null, ['class' => 'form-control', 'id' => 'tagId', 'autocomplete' => 'off', 'multiple' => 'multiple']) }}
                <div class="input-group-append plus-icon-height">
                    <div class="input-group-text">
                        <a href="#" data-toggle="modal" data-target="#addCommonTagModal"><i
                                class="fa fa-plus"></i></a>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('currency', __('messages.customer.currency') . ':') }}<span class="required">*</span>
            <select id="estimateCurrencyId" data-show-content="true" class="form-control currency-select-box"
                name="currency" required>
                <option value="0" disabled="true" {{ isset($estimate->currency) ? '' : 'selected' }}>
                    {{ __('messages.placeholder.select_currency') }}
                </option>
                @foreach ($data['currencies'] as $key => $currency)
                    <option value="{{ $key }}"
                        {{ (isset($estimate->currency) ? $estimate->currency : null) == $key ? 'selected' : '' }}>
                        &#{{ getCurrencyIcon($key) }}&nbsp;&nbsp;&nbsp; {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        {{--
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('sale_agent', __('messages.invoice.sale_agent') . ':') }}
            {{ Form::select('sales_agent_id', $data['saleAgents'], isset($estimate->sales_agent_id) ? $estimate->sales_agent_id : null, ['class' => 'form-control sale-agent-select-box', 'id' => 'saleAgentId', 'placeholder' => __('messages.placeholder.select_sale_agent')]) }}
        </div>
        <div class="form-group col-lg-4 col-md-4 col-sm-12">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') . ':') }}<span
                class="required">*</span>
            {{ Form::select('discount_type', $data['discountType'], isset($estimate->discount_type) ? $estimate->discount_type : null, ['class' => 'form-control', 'id' => 'discountTypeSelect', 'required', 'placeholder' => __('messages.placeholder.select_discount_type')]) }}
        </div> --}}


    </div>

    <hr />
    <br />


    <div class="row">
        <div class="form-group col-lg-12 col-md-12 col-sm-12 overflow-section">
            <table class="table table-responsive-sm table-responsive-md table-striped table-bordered" id="itemTable">
                <thead>
                    <tr>
                        <th>{{ __('messages.estimate.item') }}<span class="required">*</span></th>
                        {{-- <th class="small-column"><span class="qtyHeader">{{ __('messages.estimate.qty') }}</span><span
                                class="required">*</span></th> --}}
                        <th class="small-column">{{ __('messages.estimate.rate') }}<span class="required">*</span></th>
                        {{-- <th class="medium-column">{{ __('messages.estimate.taxes') }}(<i
                                class="fas fa-percentage"></i>)</th> --}}
                        {{-- <th class="small-column">{{ __('messages.estimate.amount') }}<span class="required">*</span> --}}
                        </th>
                        <th class="button-column">Remarks</th>
                        <th class="button-column"><a id="EmployeeAddAddBtn"></a></th>
                    </tr>
                </thead>
                <tbody class="items-container">
                    @foreach ($employeeQuotations as $item)
                        <tr>
                            <td style="width: 40%;">
                                <div >
                                    {{ Form::select('employee_id[]', $employees, $item->employee_id, [
                                        'class' => 'form-control',
                                        'required',
                                        'id' => 'employee_select',
                                    ]) }}
                                </div>
                            </td>
                            {{-- <td>
                                {{ Form::text('quantity[]', $item->hours, ['class' => 'form-control qty', 'required', 'min' => '0', 'placeholder' => __('messages.estimate.qty')]) }}
                            </td> --}}
                            <td>
                                {{ Form::text('rate[]', $item->rate, ['class' => 'form-control rate', 'required', 'placeholder' => __('messages.estimate.rate')]) }}
                            </td>
                            <td>
                                {{ Form::text('remarks[]', $item->remarks, ['class' => 'form-control rate']) }}
                            </td>

                            {{-- <td class="">
                                {{ Form::select('tax[]', $data['taxesArr'], $item->taxes, ['class' => 'form-control tax-rates']) }}
                            </td>
                            <td class="item-amount-width"><span
                                    class="item-amount">{{ number_format($item->total) }}</span></td> --}}
                            <td><a href="#" class="remove-invoice-item text-danger"><i
                                        class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{--
    <div class="row">
        <div class="form-group col-lg-2 col-md-6 col-sm-12">
            {{ Form::label('sub_total', __('messages.invoice.sub_total') . ':') }}
            <p> <span id="subTotal">{{ $estimate->sub_total }}</span></p>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="fDiscount form-group">
                {{ Form::label('discount', __('messages.invoice.discount') . ':') }}
                ( <span class="footer-discount-numbers">{{ formatNumber($estimate->discount) }}</span>)
                <div class="input-group">
                    {{ Form::text('final_discount', $estimate->discount, ['class' => 'form-control footer-discount-input', 'placeholder' => __('messages.invoice.discount')]) }}
                    <div class="input-group-append">
                        @if (isset($estimate->discount_type) && $estimate->discount_type === 0)
                            <input type="hidden" name="discount_symbol" value="0">
                        @endif
                        <select class="input-group-text dropdown" id="footerDiscount" name="discount_symbol">
                            <div class="dropdown-menu">
                                <option value="1" class="dropdown-item"
                                    {{ isset($estimate->discount_symbol) && $estimate->discount_symbol == 1 ? 'selected' : '' }}>
                                    %
                                </option>
                                <option value="2" class="dropdown-item"
                                    {{ isset($estimate->discount_symbol) && $estimate->discount_symbol == 2 ? 'selected' : '' }}>
                                    {{ __('messages.invoice.fixed') }}</option>
                            </div>
                        </select>
                    </div>
                </div>
            </div>
            <table id="taxesListTable" class="w-100">
                @foreach ($estimate->salesTaxes as $tax)
                    <tr>
                        <td colspan="2" class="font-weight-bold tax-value">{{ $tax->tax }}%</td>
                        <td class="footer-numbers footer-tax-numbers">{{ $tax->amount }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('adjustment', __('messages.estimate.adjustment') . ':') }}
            ( <span class="adjustment-numbers">{{ number_format($estimate->adjustment) }}</span>)
            {{ Form::number('adjustment', $estimate->adjustment, ['class' => 'form-control', 'id' => 'adjustment', 'placeholder' => __('messages.estimate.adjustment')]) }}
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12">
            {{ Form::label('total', __('messages.invoice.total') . ':') }}
            <p><span class="total-numbers">{{ number_format($estimate->total_amount) }}</span></p>
        </div>
    </div> --}}

    <div class="row">
        <div class="form-group col-lg-12 col-md-12 col-sm-12">
            {{ Form::label('terms_conditions', __('messages.invoice.terms_conditions') . ':') }}
            {{ Form::textarea('term_conditions', isset($estimate->term_conditions) ? nl2br(e($estimate->term_conditions)) : null, ['class' => 'form-control summernote-simple', 'id' => 'editTermAndConditions']) }}
        </div>
        <div class="form-group col-sm-12">

            {{-- <a href="{{ url()->previous() }}"
                class="btn btn-secondary text-dark ml-3">{{ __('messages.common.cancel') }}</a> --}}
        </div>
    </div>
    <div class="row justify-content-end mr-1">
        <div class="btn-group dropup open">
            {{ Form::button('Save', ['class' => 'btn btn-primary', 'id' => 'editSaveSend']) }}
        </div>
    </div>
</div>
