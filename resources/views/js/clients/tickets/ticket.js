'use strict'

let tableName = '#clientTicketTbl'
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
    order: false,
    ajax: {
        url: route('client.tickets.index'),
        beforeSend: function () {
            startLoader()
        },
        complete: function () {
            stopLoader()
        },
    },
    columnDefs: [
        {
            'targets': [3],
            'orderable': false,
            'className': 'text-center',
            'width': '6%',
        },
        {
            'targets': [2],
            'width': '15%',
            'orderable': false,
        },
        {
            targets: '_all',
            defaultContent: 'N/A',
        },
    ],
    columns: [
        {
            data: function (row) {
                let showPageUrl = route('client.tickets.show', row.id)
                let element = document.createElement('textarea');
                element.innerHTML = row.subject;
                let subject = element.value;
                return `<a href="${showPageUrl}" class="text-decoration-none">${subject}</a>`
            },
            name: 'subject',
        },
        {
            data: 'email',
            name: 'email',
        },
        {
            data: function (row) {
                return `<span class="badge badge-primary" style="background-color: ${row.ticket_status.pick_color};">${row.ticket_status.name}</span>`
            },
            name: 'id',
        },
        {
            data: function (row) {
                let data = [
                    {
                        'id': row.id,
                    }]
                return prepareTemplateRender('#clientTicketActionTemplate',
                    data)
            }, name: 'id',
        },
    ],
})

$(document).on('click', '.delete-client-ticket', function () {
    let deleteTicketID = $(this).attr('data-id')

    deleteItem(route('client.tickets.destroy', deleteTicketID), tableName,
        Lang.get('messages.task.ticket'))
})
