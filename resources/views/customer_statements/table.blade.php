<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="designationTable">
    <thead>
        <tr>
            <th>{{ __('messages.branches.name') }}</th>
            <th>{{ __('messages.customer_statements.invoice_date') }}</th>
            <th>{{ __('messages.customer_statements.invoice_number') }}</th>
            <th>Type</th>
            <th>{{ __('messages.customer_statements.receipt_date') }}</th>
            <th>{{ __('messages.customer_statements.month') }}</th>
            <th>{{ __('messages.customer_statements.project_name') }}</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>{{ __('messages.customer_statements.balance') }}</th>

        </tr>
    </thead>

    <tfoot>
        <tr>
            <th colspan="5"></th> <!-- Empty columns for the first 5 columns -->
            <th></th>
            <th class="text-right">Total</th>

            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
