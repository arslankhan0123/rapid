<div class="row">
    <div class="form-group col-sm-12  col-md-3">
        {{ Form::label('employee_id', __('messages.branches.name') . ':') }}
        {{ Form::select('branch_id', $usersBranches ?? [], $expense->branch_id ?? null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}
    </div>
    <div class="form-group col-md-3 col-sm-12">
        {{ Form::label('expense', __('messages.expense.expense_number') . ':') }}<span class="required">*</span>
        {{ Form::text('expense_number', $expense->expense_number ?? null, ['class' => 'form-control', 'required', 'readonly']) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('expense_category_id', __('messages.expense.expense_category')) }}<span class="required">*</span>
        <div class="input-group">
            {{ Form::select('expense_category_id', $data['expenseCategories'], null, ['id' => 'expenseCategory', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_expanse_category'), 'required']) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addExpenseCategoryModal"><i
                            class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('sub Category', __('messages.expense.sub_category')) }}
        {{ Form::select('sub_category_id', $subCategories->pluck('name', 'id') ?? [], $expense->sub_category_id ?? null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Sub Category', 'id' => 'subCategory']) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('name', __('messages.expense.name')) }}<span class="required">*</span>
        {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.expense.name')]) }}
    </div>

    <div class="form-group col-md-3">
        {{ Form::label('expense_date', __('messages.expense.expense_date')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            {{ Form::text('expense_date', null, ['class' => 'form-control datepicker', 'placeholder' => __('messages.expense.expense_date')]) }}
        </div>
    </div>

    <div class="form-group col-md-3">
        {{ Form::label('amount', __('messages.expense.amount')) }}<span class="required">*</span>
        <div class="input-group">
            {{ Form::text('amount', null, ['class' => 'form-control price-input', 'id' => 'amount', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.expense.amount')]) }}
        </div>
    </div>

    <div class="form-group col-md-3">
        {{ Form::label('currency', __('messages.expense.currency')) }}
        {{ Form::select('currency', $currencies, $expense->currency, ['id' => 'currency', 'class' => 'form-control']) }}
    </div>

    <div class="form-group col-md-3">
        {{ Form::label('payment_mode_id', __('messages.expense.payment_mode')) }}
        {{ Form::select('payment_mode_id', $accounts->pluck('account_name', 'id') ?? [], $expense->payment_mode_id ?? (null ?? ''), ['id' => 'paymentModeNew', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.select_payment_mode')]) }}

        {{-- <div class="input-group">
            {{ Form::select('payment_mode_id', $data['paymentModes'], null, ['id' => 'paymentMode', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_payment_mode')]) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addCommonPaymentModeModal"><i
                            class="fa fa-plus"></i></a>
                </div>
            </div>
        </div> --}}
    </div>


    <div class="form-group col-md-3">
        {{ Form::label('tax_1_id', __('messages.expense.tax_1')) }}
        {{ Form::select('tax_1_id', $data['taxRates'], null, ['class' => 'form-control readonly-select', 'placeholder' => __('messages.placeholder.select_tax')]) }}

    </div>

    <div class="form-group col-md-3 d-none">
        {{ Form::label('reference', __('messages.expense.reference')) }}
        {{ Form::text('reference', $expense->reference, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.expense.reference')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('customer_id', 'Supplier') }}
        {{ Form::select('supplier_id', $suppliers?->pluck('company_name', 'id') ?? [], $expense->supplier_id ?? null, ['id' => 'selectSupplier', 'class' => 'form-control', 'placeholder' => 'Select Supplier']) }}
    </div>

    <div class="form-group col-md-3">
        {{ Form::label('customer_id', __('messages.expense.customer')) }}
        {{ Form::select('customer_id', $data['customers'], $expense->customer_id, ['id' => 'customers', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_user')]) }}
    </div>


    <div class="form-group col-sm-12  col-md-3">
        {{ Form::label('employee_id', __('messages.expense.pur_inv_number')) }}
        {{ Form::text('pur_inv_number', $expense->pur_inv_number ?? '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12  col-md-3">
        {{ Form::label('employee_id', __('messages.expense.supp_vat_number')) }}
        {{ Form::text('supp_vat_number', $expense->supp_vat_number ?? '', ['class' => 'form-control', 'id' => 'supp_vat_number', 'autocomplete' => 'off', $expense->supplier_id ? 'readonly' : '']) }}
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for="customer">{{ __('messages.expense.expense_by') }}</label>
        <div class="input-group">
            <!-- Customer Name Input (70% width) -->
            <input type="text" id="employeeInmput" name="employee_name" class="form-control"
                value="{{ $expense->employee_name ?? '' }}">
            <!-- Customer ID Dropdown (30% width) -->
            <div class="input-group-append">
                {{ Form::select('employee_id', $employees->pluck('name', 'id'), $expense->employee_id ?? null, ['class' => 'form-control select2', 'id' => 'employeeSelect', 'style' => 'width:150px;', 'placeholder' => 'Select Employee']) }}
            </div>
        </div>
    </div>
    <div class="form-group col-sm-12  col-md-3">
        {{ Form::label('employee_id', __('messages.expense.expense_for')) }}
        {{ Form::text('expense_for', $expense->expense_for ?? '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
    </div>

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
                            <th scope="col" class=" d-none">{{ __('messages.employees.expiry_date') }}</th>
                            <th scope="col" class="text-right">{{ __('messages.common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expense->documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->file }}</td>
                                <td class=" d-none">{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="text-right">
                                    <a href="{{ asset('uploads/public/expense_docs/' . basename($document->file)) }}"
                                        class="btn btn-outline-primary" target="_blank" title="View PDF">
                                        <i class="fas fa-eye"></i>

                                    </a>
                                    <button type="button" class="btn btn-outline-danger fileDeleteBtn"
                                        data-toggle="modal" data-id="{{ $document->id }}" title="Delete Document">
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
                    <button type="button" id="addField" class="btn btn-primary mb-3 float-right"><i class="fa fa-plus"
                            aria-hidden="true"></i></button>
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
                    <div class="col-md-3  d-none">
                        <label for="expiry_date" class="form-label">Expiry Date:</label>
                        <input type="date" name="expiry_date[]" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeField" style="display:none;">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-sm-12">
        {{ Form::label('note', __('messages.expense.note')) }}
        {{ Form::textarea('note', $expense->note, ['class' => 'form-control summernote-simple', 'id' => 'expenseNote']) }}
    </div>

    <div class="form-group col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="row no-gutters">
            <div class="col-6">
                {{ Form::label('receipt_attachment', __('messages.expense.attachment'), ['class' => 'profile-label-color']) }}
                @if (isset($expense->media[0]))
                    <a href="{{ $expense->media[0]->getFullUrl() }}" target="_blank" class="attachments-view">
                        {{ __('messages.common.view') }}
                    </a>
                @endif
                <label class="image__file-upload"> {{ __('messages.setting.choose') }}
                    {{ Form::file('receipt_attachment', ['id' => 'attachment', 'class' => 'd-none']) }}
                </label>
            </div>
            <div class="col-2 mt-1 ml-1">
                <img id='previewImage' class="img-thumbnail thumbnail-preview tPreview"
                    src="{{ isset($expense->media[0]) ? $expense->media[0]->getFullUrl() : asset('assets/img/infyom-logo.png') }}" />
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row justify-content-between mr-1">
    <div class="form-group col-sm-2">
        <div class="">
            <input type="checkbox" id="isTaxable" name="isTaxable" style="transform: scale(1.8);"
                {{ $expense->isTaxable == true ? 'Checked' : '' }}>
            <label class="ml-3"style="line-height: 10px;">{{ __('messages.expense.apply_vat') }}</label>
        </div>
    </div>
    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
    {{-- <a href="{{ route('expenses.index') }}" class="btn btn-secondary text-dark">{{ __('messages.common.cancel') }}</a> --}}
</div>
<script>
    $(document).ready(function() {
        $('.datepicker').datetimepicker({
            format: 'DD-MM-YYYY', // Set the format to dd-mm-yy
            useCurrent: false, // Disable auto-updating of the field
        });
    });
</script>
