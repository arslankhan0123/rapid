@extends('expenses.show')

@section('section')
    @if (isset($expense) && $expense->billable == 1)
        <div class="row">
            <div class="form-group col-6 col-sm-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck1" name="billable" value="1"
                        {{ isset($expense) && $expense->billable == 1 ? 'checked' : '' }} disabled>
                    <label class="custom-control-label" for="customCheck1">{{ __('messages.task.billable') }}</label>
                </div>
            </div>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12 text-right mb-3">
            @can('export_expenses')
                <a href="{{ route('expenses.pdf', $expense->id) }}" class="btn btn-info">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            @endcan

        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.branches.name')) }}
                <p>{{ $expense->branch?->name ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('expense_number', __('messages.expense.expense_number')) }}
                <p>{{ $expense->expense_number ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('expense_category_id', __('messages.expense.expense_category')) }}
                <p>{{ html_entity_decode($expense->expenseCategory?->name ?? '') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('expense_category_id', __('messages.expense.sub_category')) }}
                <p>{{ html_entity_decode($expense->expenseSubCategory?->name ?? '') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('name', __('messages.expense.name')) }}
                <p>{{ isset($expense->name) ? html_entity_decode($expense->name) : __('messages.common.n/a') }}</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('expense_date', __('messages.expense.expense_date')) }}
                <p>{{ Carbon\Carbon::parse($expense->expense_date)->translatedFormat('jS M, Y') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('amount', __('messages.expense.amount')) }}
                <p> {{ number_format($expense->amount, 2) }}</p>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('amount', __('messages.expense.amount_with_tax')   ) }}
                <p>
                    @if ($expense->tax_rate != 0)

                        {{ number_format($expense->tax_rate, 2) }}
                    @else
                        {{ __('messages.common.n/a') }}
                    @endif
                </p>
            </div>
        </div> --}}


        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.expense.currency')) }}
                <p>{{ $expense->currencyNew?->name ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4 form-group">
            {{ Form::label('payment_mode_id', __('messages.expense.payment_mode')) }}
            <p>{{ isset($expense->paymentMode->account_name) ? html_entity_decode($expense->paymentMode->account_name) : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('tax_1', __('messages.expense.tax_1')) }}
                <p>{{ isset($expense->tax1Rate) ? $expense->tax1Rate->tax_rate : __('messages.common.n/a') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('reference', 'Supplier') }}
                <p>
                <p>{{ $expense->supplier?->company_name ?? '' }}</p>
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('customer_id', __('messages.expense.customer')) }}
                <p>
                    @if (isset($expense->customer))
                        <a class="anchor-underline">{{ $expense->customer?->name ?? '' }}</a>
                    @else
                        {{ __('messages.common.n/a') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.expense.pur_inv_number')) }}
                <p>{{ $expense->pur_inv_number ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.expense.supp_vat_number')) }}
                <p>{{ $expense->supp_vat_number ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.expense.expense_by')) }}
                <p>{{ $expense->employee_name ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.expense.expense_for')) }}
                <p>{{ $expense->expense_for ?? '' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('currency', __('messages.expense.apply_vat')) }}
                <p>{{ $expense->isTaxable == true ? 'Yes' : 'No' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('receipt_attachment', __('messages.expense.attachment')) }}
                <br>
                @if (isset($expense->media[0]))
                    <a href="{{ url('admin/expense-download-media', $expense->media[0]) }}"
                        class="text-decoration-none">{{ __('messages.common.download') }}
                    </a>
                @else
                    <p>{{ __('messages.common.n/a') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('created_at', __('messages.common.created_on')) }}<br>
                <span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($expense->created_at)->translatedFormat('jS M, Y') }}">{{ $expense->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('updated_at', __('messages.common.last_updated')) }}<br>
                <span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($expense->updated_at)->translatedFormat('jS M, Y') }}">{{ $expense->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('note', __('messages.expense.note')) }}
                <br>{!! isset($expense->note) ? html_entity_decode($expense->note) : __('messages.common.n/a') !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class=" col  ">
            <div
                style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                <h4 class="text-center">{{ __('messages.employees.manage_documents') }}</h4>
            </div>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ __('messages.employees.documents') }}</th>
                        <th style="width: 60%;">{{ __('messages.employees.files') }}</th>
                        <th style="width: 35%;">{{ __('messages.employees.expiry_date') }}</th>
                        <th style="width: 10%;" class=" d-none">{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expense->documents as $document)
                        <tr>
                            <td>{{ $document->name }}</td>
                            <td>{{ $document->file }}</td>
                            <td class=" d-none">{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ asset('uploads/public/expense_docs/' . basename($document->file)) }}"
                                    class="btn btn-outline-primary" target="_blank" title="View PDF">
                                    <i class="fas fa-eye"></i>

                                </a>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
@endsection
