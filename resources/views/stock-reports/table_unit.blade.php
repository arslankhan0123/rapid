{{-- <table
    class="table table-responsive-xs table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="assetCategoryTable">
    <thead>
        <tr>
            <th></th>
            <th scope="col">{{ __('messages.common.code') }}</th>
            <th scope="col">{{ __('messages.common.barcode') }}</th>
            <th scope="col">{{ __('messages.purchase-items.short_name') }}</th>
            <th scope="col">{{ __('messages.common.groups') }}</th>
            <th scope="col">{{ __('messages.common.categories') }}</th>
            <th scope="col">{{ __('messages.common.sub_categories') }}</th>
            <th scope="col">{{ __('messages.assets.category_description') }}</th>
            <th scope="col" style="text-align: right;">{{ __('messages.common.action') }}</th>
            <th scope="col" style="display: none;"></th> <!-- Hidden column for created_at -->
        </tr>
    </thead>
    <tbody>
    </tbody>
</table> --}}

<table
    class="table table-responsive-xs table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="assetCategoryTable">
    <thead>
        <tr>
            <th scope="col">SL</th>
            <th scope="col">{{ __('messages.stock-reports.item_code') }}</th>
            <th scope="col">{{ __('messages.stock-reports.barcode') }}</th>
            <th scope="col">{{ __('messages.stock-reports.item_name') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.qty_in') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.qty_out') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.qty_current') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.cost_price') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.amount') }}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    {{-- <tfoot>
        <tr>
            <th scope="col">SL</th>
            <th scope="col">{{ __('messages.stock-reports.item_code') }}</th>
            <th scope="col">{{ __('messages.stock-reports.barcode') }}</th>
            <th scope="col">{{ __('messages.stock-reports.item_name') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.qty_in') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.qty_out') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.qty_current') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.cost_price') }}</th>
            <th scope="col" class="text-right">{{ __('messages.stock-reports.amount') }}</th>
        </tr>
    </tfoot> --}}
</table>
