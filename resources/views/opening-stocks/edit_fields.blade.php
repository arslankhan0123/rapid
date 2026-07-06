<input type="hidden" id="estimateId" value="{{ $estimate->id }}">
<div class="card-body">
    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>

    <div class="row page_header">
        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12"> --}}
        {{-- {{ Form::label('title', __('messages.estimate.title') . ':') }}<span class="required">*</span> --}}
        {{ Form::hidden('title', isset($estimate->title) ? $estimate->title : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofocus', 'placeholder' => __('messages.products.title')]) }}
        {{-- </div> --}}
        <div class="form-group col-lg-3 col-md-4 col-sm-12">
            {{ Form::label('estimate_number', __('messages.purchase-orders.order_number') . ':') }}<span
                class="required">*</span>
            {{ Form::text('estimate_number', isset($estimate->estimate_number) ? $estimate->estimate_number : rand(5000, 10000), ['class' => 'form-control', 'required', 'id' => 'estimateNumber', 'placeholder' => __('messages.estimate.estimate_number')]) }}
        </div>
        <div class="form-group col-lg-3 col-md-4 col-sm-12">
            {{ Form::label('estimate_date', __('messages.purchase-orders.order_date') . ':') }} <span
                class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="height:38px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_date', isset($estimate->estimate_date) ? date('Y-m-d H:i:s', strtotime($estimate->estimate_date)) : null, ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <label for="customer">{{ __('messages.purchase-orders.supplier') }}:</label><span class="required">*</span>
            <div class="input-group">
                <!-- Customer Name Input (70% width) -->
                <input type="text" id="customerNameInput" name="customer_name"
                    value="{{ $estimate->customer_name ?? null }}" class="form-control" required>

                <!-- Customer ID Dropdown (30% width) -->
                <div class="input-group-append">
                    {{ Form::select('customer_id', $data['customers'], $estimate->customer_id ?? null, ['class' => 'form-control', 'id' => 'customerSelectBoxNew', 'placeholder' => 'Select Customer']) }}
                </div>
            </div>
        </div>

        <div class="form-group col-lg-3  col-sm-12">
            {{ Form::label('expiry_date', __('messages.estimate.expiry_date') . ':') }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="height:38px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::text('estimate_expiry_date', isset($estimate->estimate_expiry_date) ? date('Y-m-d H:i:s', strtotime($estimate->estimate_expiry_date)) : null, ['class' => 'form-control due-datepicker', 'autocomplete' => 'off']) }}
            </div>
        </div>

        <div class="form-group col-md-3 col-sm-12">
            {{ Form::label('currency', __('messages.customer.currency') . ':') }}<span class="required">*</span>
            <select id="estimateCurrencyId" data-show-content="true" class="form-control currency-select-box"
                name="currency" required>
                <option value="0" disabled="true" {{ isset($estimate->currency) ? '' : 'selected' }}>
                    {{ __('messages.placeholder.select_currency') }}
                </option>
                @foreach ($data['currencies'] as $key => $currency)
                    <option value="{{ $key }}"
                        {{ (isset($estimate->currency) ? $estimate->currency : null) == $key ? 'selected' : '' }}>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3 col-sm-12">
            {{ Form::label('reference', __('messages.credit_note.reference') . ':') }}
            {{ Form::text('reference', isset($estimate->reference) ? $estimate->reference : null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-lg-2 col-md-3 col-sm-12 ">
            <a href="#" class="mr-3 addressModalIcon"><i class="fa fa-edit"></i></a>
            {{ Form::label('bill_to', __('messages.invoice.bill_to') . ':') }}
            <div id="bill_to" class="ml-5">
                _ _ _ _ _ _
            </div>
        </div>

        <div class="form-group col-lg-12 col-md-12 col-sm-12">
            {{ Form::label('admin_note', 'Subject:') }}
            {{ Form::textarea('admin_note', isset($estimate->admin_note) ? nl2br(e($estimate->admin_note)) : null, ['class' => 'form-control', 'id' => 'editAdminNote']) }}
        </div>
        <div class="form-group col-lg-12 col-md-12 col-sm-12">
            {{ Form::label('client_note', 'Message:') }}
            {{ Form::textarea('client_note', isset($estimate->client_note) ? nl2br(e($estimate->client_note)) : null, ['class' => 'form-control summernote-simple', 'id' => 'editClientNote']) }}
        </div>
    </div>

    <br>
    @include('opening-stocks.edit_items')
    <br>


    <div class="row float-right">
        {{-- <a href="{{ url()->previous() }}"
            class="btn btnSecondary text-white mr-3">{{ __('messages.common.cancel') }}</a> --}}
        {{ Form::button('Save', ['type'=>"Submit",'class' => 'btn btn-primary', 'style' => 'line-height:31px;']) }}
        {{-- <div class="btn-group dropup open">

            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-left width200">
                <li>
                    <a href="#" class="dropdown-item" id="editSaveSend"
                        data-status="1">{{ __('messages.estimate.save_and_send') }}</a>
                </li>
            </ul>
        </div> --}}


    </div>
</div>
