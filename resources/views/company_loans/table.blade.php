<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="companyLoansTable">
    <thead>
        <tr>
            <th scope="col">{{ __('messages.company_loans.serial_no') }}</th>
            <th scope="col">{{ __('messages.company_loans.date') }}</th>
            <th scope="col">{{ __('messages.company_loans.type') }}</th>
            <th scope="col">{{ __('messages.company_loans.name') }}</th>
            <th scope="col">{{ __('messages.company_loans.loan_amount') }}</th>
            <th scope="col">{{ __('messages.company_loans.loan_received_date') }}</th>
            <th scope="col">{{ __('messages.company_loans.loan_refund_date') }}</th>
            <th scope="col">{{ __('messages.company_loans.number_of_days') }}</th>
            <th scope="col">{{ __('messages.common.action') }}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" style="text-align:right">{{ __('messages.company_loans.total_loan_amount') }}:</th>
            <th id="loanAmountFooter" style="text-align:right">0.00</th>
            <th colspan="4"></th>
        </tr>
    </tfoot>
</table>
