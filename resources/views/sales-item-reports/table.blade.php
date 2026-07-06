<table class="table table-striped table-bordered" id="salesItemReportTable">
    <thead>
        <tr>
            <th width="5%">SN</th>
            <th>{{ __('messages.sales_item_reports.invoice_no') }}</th>
            <th>{{ __('messages.sales_item_reports.customer') }}</th>
            <th>{{ __('messages.sales_item_reports.sales_date') }}</th>
            <th>{{ __('messages.sales_item_reports.item') }}</th>
            <th class="text-right">{{ __('messages.sales_item_reports.quantity') }}</th>
            <th class="text-right">{{ __('messages.sales_item_reports.unit_price') }}</th>
            <th class="text-right">{{ __('messages.sales_item_reports.total') }}</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th colspan="5" class="text-right">Totals:</th>
            <th class="text-right"></th>
            <th class="text-right"></th>
            <th class="text-right"></th>
        </tr>
    </tfoot>
    <tbody></tbody>
</table>
