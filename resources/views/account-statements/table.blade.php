<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="designationTable" style="width: 100%;">
    <thead>
        <tr>
            <th>{{ __('messages.employee-statements.date') }}</th>
            <th>{{ __('messages.employee-statements.number') }}</th>
            <th>{{ __('messages.employee-statements.type') }}</th>
            <th>{{ __('messages.employee-statements.narration') }}</th>
            <th>{{ __('messages.employee-statements.debit') }}</th>
            <th>{{ __('messages.employee-statements.credit') }}</th>
            <th>{{ __('messages.employee-statements.balance') }}</th>

        </tr>
    </thead>
    <tfoot>
        <tr>
            <th ></th>
            <th></th>
            <th></th>
            <th class="float-right border-0">Total</th>
            <th id="total-debit" class="pr-2"></th>
            <th id="total-credit" class="pr-2"></th>
            <th id="total-balance" class="pr-2"></th>
        </tr>
    </tfoot>


</table>
