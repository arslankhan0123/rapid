<!DOCTYPE html>
<html>

<head>
    <title>Payment Voucher Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h1>Payment Voucher Details</h1>

    <table>
        <tr>
            <th>{{ __('messages.branches.name') }}</th>
            <td>{{ $expense->branch?->name ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.expense_number') }}</th>
            <td>{{ $expense->expense_number ?? '' }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.expense_category') }}</th>
            <td>{{ html_entity_decode($expense->expenseCategory?->name ?? '') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.sub_category') }}</th>
            <td>{{ html_entity_decode($expense->expenseSubCategory?->name ?? '') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.name') }}</th>
            <td>{{ isset($expense->name) ? html_entity_decode($expense->name) : __('messages.common.n/a') }}</td>
        </tr>

        <tr>
            <th>{{ __('messages.expense.expense_date') }}</th>
            <td>{{ Carbon\Carbon::parse($expense->expense_date)->translatedFormat('jS M, Y') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.amount') }}</th>
            <td> {{ number_format($expense->amount, 2) }}</td>
        </tr>
        {{-- <tr>
            <th>{{ __('messages.expense.amount_with_tax') }}</th>
            <td>
                @if ($expense->tax_rate != 0)
                    <i class="{{ getCurrencyClass() }}"></i> {{ number_format($expense->tax_rate, 2) }}
                @else
                    {{ __('messages.common.n/a') }}
                @endif
            </td>
        </tr> --}}
        <tr>
            <th>{{ __('messages.expense.currency') }}</th>
            <td>{{ $expense->currencyNew->name ?? '' }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.payment_mode') }}</th>
            <td>{{ isset($expense->paymentMode->account_name) ? html_entity_decode($expense->paymentMode->account_name) : __('messages.common.n/a') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.tax_1') }}</th>
            <td>{{ isset($expense->tax_1_id) ? $expense->tax1Rate->tax_rate : __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td>{{ $expense->supplier?->company_name ?? __('messages.common.n/a') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.customer') }}</th>
            <td>
                @if (isset($expense->customer))
                    {{ $expense->customer?->name ?? '' }}
                @else
                    {{ __('messages.common.n/a') }}
                @endif
            </td>
        </tr>

        <tr>
            <th>{{ __('messages.expense.pur_inv_number') }}</th>
            <td>{{ $expense->pur_inv_number ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.supp_vat_number') }}</th>
            <td>{{ $expense->supp_vat_number ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.expense_by') }}</th>
            <td>{{ $expense->employee_name ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.expense_for') }}</th>
            <td>{{ $expense->expense_for ?? __('messages.common.n/a') }}</td>
        </tr>

        <tr>
            <th>{{ __('messages.expense.apply_vat') }}</th>
            <td>{{ $expense->isTaxable == true ? 'Yes' : 'No' }}</td>
        </tr>

{{--
        <tr>
            <th>{{ __('messages.expense.currency') }}</th>
            <td>{{ \App\Models\Customer::CURRENCIES[$expense->currency] }}</td>
        </tr> --}}
        <tr>
            <th>{{ __('messages.expense.tax_1') }}</th>
            <td>{{ isset($expense->tax_1_id) ? $expense->tax1Rate->tax_rate : __('messages.common.n/a') }}</td>
        </tr>




        <tr>
            <th>{{ __('messages.common.created_on') }}</th>
            <td>
                {{ $expense->created_at->translatedFormat('jS M, Y') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.common.last_updated') }}</th>
            <td>
                {{ $expense->updated_at->translatedFormat('jS M, Y') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.expense.note') }}</th>
            <td>{!! isset($expense->note) ? html_entity_decode($expense->note) : __('messages.common.n/a') !!}</td>
        </tr>
    </table>


</body>

</html>
