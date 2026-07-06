<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="designationTable">
    <thead>
        <tr>
            <th scope="col">{{ __('messages.branches.name') }}</th>
            <th scope="col">{{ __('messages.expense.expense_date') }}</th>
            <th scope="col">{{ __('messages.expense.expense_number') }}</th>
            <th scope="col">{{ __('messages.expense.name') }}</th>

            <th scope="col">{{ __('messages.expense.expense_by') }}</th>
            <th scope="col">{{ __('messages.expense.payment_mode') }}</th>
            <th scope="col">{{ __('messages.expense.expense_category') }}</th>
            <th scope="col">Sub Category</th>
            <th scope="col">Supplier</th>
            <th scope="col" style="width: 100%;text-align:end;">{{ __('messages.expense.amount') }}</th>
            <th scope="col" style="width: 100%;text-align:end;">{{ __('messages.common.action') }}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5"></th>
            <th></th> <!-- The total will be inserted here -->
            <th></th>
            <th></th>
            <th style="text-align:right">Total</th>

            <th></th>

            <th></th>

        </tr>
    </tfoot>

</table>
