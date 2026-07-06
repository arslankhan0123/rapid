{{-- <table
    class="table table-responsive-xs table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="assetCategoryTable">
    <thead>
        <tr>

            <th>Branch</th>

            <th scope="col">{{ __('messages.vat-reports.period') }}</th>
            <th scope="col">{{ __('messages.vat-reports.input') }}</th>
            <th scope="col">{{ __('messages.vat-reports.output') }}</th>
            <th scope="col">{{ __('messages.vat-reports.net') }}</th>
            <th scope="col">{{ __('messages.vat-reports.paid') }}</th>
            <th scope="col">{{ __('messages.vat-reports.unpaid') }}</th>
            <th scope="col" style="text-align: right;">{{ __('messages.common.action') }}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th> <!-- No total for action column -->
        </tr>
    </tfoot>
</table> --}}

<table
    class="table table-responsive-xs table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="assetCategoryTable">
    <thead>
        <tr>
            <th scope="col">{{ __('messages.vat-reports.period') }}</th>
            <th scope="col">{{ __('messages.vat-reports.input') }}</th>
            <th scope="col">{{ __('messages.vat-reports.output') }}</th>
            <th scope="col">{{ __('messages.vat-reports.net') }}</th>
            <th scope="col">{{ __('messages.vat-reports.paid') }}</th>
            <th scope="col">{{ __('messages.vat-reports.unpaid') }}</th>
            <th scope="col" style="text-align: right;">{{ __('messages.common.action') }}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th> <!-- No total for action column -->
        </tr>
    </tfoot>
</table>
