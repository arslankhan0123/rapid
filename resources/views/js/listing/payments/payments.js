'use strict';

let tableName = '#paymentsTbl';
$(tableName).DataTable({
    oLanguage: {
        'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
        'sInfo': Lang.get('messages.common.data_base_entries'),
        sLengthMenu: Lang.get('messages.common.menu_entry'),
        sInfoEmpty: Lang.get('messages.common.no_entry'),
        sInfoFiltered: Lang.get('messages.common.filter_by'),
        sZeroRecords: Lang.get('messages.common.no_matching'),
    },
    processing: true,
    serverSide: true,
    'order': [[2, 'desc']],
    ajax: {
        url: route('payments.list.index'),
        data: function (data) {
            data.owner_type = ownerType;
        },
    },
    columnDefs: [
        {
            'targets': [4],
            'className': 'text-right',
            'width': '10%',
        },
        {
            'targets': [0, 2, 3],
            'width': '15%',
        },
        {
            targets: '_all',
            defaultContent: 'N/A',
        },
    ],
    columns: [
        {
            data: 'payment_mode.name',
            name: 'paymentMode.name',
        },
        {
            data: function (row) {
                let element = document.createElement('textarea');
                element.innerHTML = row.note;
                if (element.value != '')
                    return element.value;
                else
                    return 'N/A';
            },
            name: 'note',
        },
        {
            data: function (row) {
                return row;
            },
            render: function (row) {
                return moment(row.payment_date).format('Do MMM, Y h:mm A');
            },
            name: 'payment_date',
        },
        {
            data: 'transaction_id',
            name: 'transaction_id',
        },
        {
            data: function (row) {
                return '<i class="' + currentCurrencyClass + '"></i>' + ' ' +
                    getFormattedPrice(row.amount_received) +
                    '</i>';
            },
            name: 'amount_received',
        },
    ],
});
