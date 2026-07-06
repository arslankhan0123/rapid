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
        url: route('payments.index'),
        data: function (data) {
            data.owner_type = ownerType;
            data.owner_id = invoiceId;
        },
    },
    columnDefs: [
        {
            'targets': [0, 2, 4],
            'width': '15%',
        },
        {
            'targets': [3],
            'className': 'text-right',
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
            name: 'payment_mode',
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
            data: function (row) {
                return '<i class="' + currentCurrencyClass + '"></i>' + ' ' +
                    getFormattedPrice(row.amount_received) +
                    '</i>';
            },
            name: 'amount_received',
        },
        {
            data: function (row) {
                if (isEmpty(row.transaction_id)) {
                    return 'N/A';
                }
                return row.transaction_id;
            },
            name: 'transaction_id',
        }
    ],
});


