<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('date', __('messages.company_loans.date') . ':') !!}<span class="text-danger">*</span>
            {!! Form::date('date', old('date', isset($companyLoan) ? $companyLoan->date : now()->format('Y-m-d')), [
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('type', __('messages.company_loans.type') . ':') !!}<span class="text-danger">*</span>
            {!! Form::select(
                'type',
                ['' => 'Select Type'] + ['customer' => 'Customer', 'supplier' => 'Supplier', 'personal' => 'Personal'],
                old('type', isset($companyLoan) ? $companyLoan->type : null),
                [
                    'class' => 'form-control select2',
                    'id' => 'typeSelect',
                    'required',
                ],
            ) !!}
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group" id="customerField" style="display: none;">
            {!! Form::label('customer_id', __('messages.company_loans.customer') . ':') !!}<span class="text-danger">*</span>
            {!! Form::select(
                'customer_id',
                ['' => 'Select Customer'] + $customers->toArray(),
                old('customer_id', isset($companyLoan) ? $companyLoan->customer_id : null),
                [
                    'class' => 'form-control select2',
                    'id' => 'customerSelect',
                    'style' => 'width:100%', // 🔥 ensures full width
                ],
            ) !!}
        </div>
        <div class="form-group" id="supplierField" style="display: none;">
            {!! Form::label('supplier_id', __('messages.company_loans.supplier') . ':') !!}<span class="text-danger">*</span>
            {!! Form::select(
                'supplier_id',
                ['' => 'Select Supplier'] + $suppliers->toArray(),
                old('supplier_id', isset($companyLoan) ? $companyLoan->supplier_id : null),
                [
                    'class' => 'form-control select2',
                    'id' => 'supplierSelect',
                    'style' => 'width:100%', // 🔥 ensures full width
                ],
            ) !!}
        </div>
        <div class="form-group" id="personalField" style="display: none;">
            {!! Form::label('name', __('messages.company_loans.name') . ':') !!}<span class="text-danger">*</span>
            {!! Form::text('name', old('name', isset($companyLoan) ? $companyLoan->name : null), [
                'class' => 'form-control',
                'id' => 'nameInput',
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('loan_amount', __('messages.company_loans.loan_amount') . ':') !!}<span class="text-danger">*</span>
            {!! Form::number('loan_amount', old('loan_amount', isset($companyLoan) ? $companyLoan->loan_amount : null), [
                'class' => 'form-control',
                'step' => '0.01',
                'min' => '0',
                'required',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('loan_received_date', __('messages.company_loans.loan_received_date') . ':') !!}<span class="text-danger">*</span>
            {!! Form::date(
                'loan_received_date',
                old('loan_received_date', isset($companyLoan) ? $companyLoan->loan_received_date : null),
                [
                    'class' => 'form-control',
                    'required',
                ],
            ) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('loan_refund_date', __('messages.company_loans.loan_refund_date') . ':') !!}<span class="text-danger">*</span>
            {!! Form::date(
                'loan_refund_date',
                old('loan_refund_date', isset($companyLoan) ? $companyLoan->loan_refund_date : null),
                [
                    'class' => 'form-control',
                    'required',
                ],
            ) !!}
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('number_of_days', __('messages.company_loans.number_of_days') . ':') !!}<span class="text-danger">*</span>
            {!! Form::number(
                'number_of_days',
                old('number_of_days', isset($companyLoan) ? $companyLoan->number_of_days : null),
                [
                    'class' => 'form-control',
                    'readonly', // user can't edit
                ],
            ) !!}
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-right">
        {!! Form::submit(__('messages.common.submit'), ['class' => 'btn btn-primary']) !!}
    </div>
</div>
