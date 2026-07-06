'use strict';

$(document).on('click', '.addAnnouncementModal', function () {
    $('#addModal').appendTo('body').modal('show');
});

$(document).on('mouseenter', '.announcement-card', function () {
    $(this).find('.action-dropdown').removeClass('d-none');
});

$(document).on('mouseleave', '.announcement-card', function () {
    $(this).find('.action-dropdown').addClass('d-none');
    $(this).parent().trigger('click');
});

$('#announcementDate, #editAnnouncementDate').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    useCurrent: false,
    sideBySide: true,
    widgetPositioning: {
        horizontal: 'left',
        vertical: 'bottom',
    },
    icons: {
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        next: 'fa fa-chevron-right',
        previous: 'fa fa-chevron-left',
    },
    minDate: moment().subtract(1, 'days'),
});

let tableName = $('#announcementTable');
tableName.DataTable({
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
    'order': [[1, 'desc']],
    ajax: {
        url: route('announcements.index'),
        data: function (data) {
            data.status = $('#filterAnnouncementStatus').
                find('option:selected').
                val()
            data.showToClient = $('#showToClientId').
                find('option:selected').
                val()
        },
    },
    columnDefs: [
        {
            'targets': [2],
            'className': 'text-center',
            'width': '121px',
            'orderable': false,
        },
        {
            'targets': [3],
            'className': 'text-center',
            'width': '68px',
            'orderable': false,
        },
        {
            targets: '_all',
            defaultContent: 'N/A',
        },
    ],
    columns: [
        {
            data: 'subject',
            name: 'subject',
        },
        {
            data: function (row) {
                if (row.date != null) {
                    return moment(row.date, 'YYYY-MM-DD hh:mm:ss').
                        format('Do MMM, Y HH:mm A');
                }
            },
            name: 'date',
        },
        {
            data: function (row) {
                let data = [
                    {
                        'id': row.id,
                        'showToClient': row.show_to_clients,
                    }];
                return prepareTemplateRender(
                    '#announcementShowToClientTemplate', data);
            },
            name: 'show_to_clients',
        },
        {
            data: function (row) {
                let data = [
                    {
                        'id': row.id,
                        'showUrl': route('announcements.show', row.id),
                    }]
                return prepareTemplateRender('#announcementActionTemplate',
                    data)
            }, name: 'id',
        },
    ],
    'fnInitComplete': function () {
        $(document).on('change', '#filterAnnouncementStatus', function () {
            $(tableName).DataTable().ajax.reload(null, true)
        })
        $(document).on('change', '#showToClientId', function () {
            $(tableName).DataTable().ajax.reload(null, true)
        })
    },
});

$(document).on('submit', '#addNewForm', function (e) {
    e.preventDefault();
    processingBtn('#addNewForm', '#btnSave', 'loading');

    let $description = $('<div />').
        html($('#createMessage').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';

    if ($('#createMessage').summernote('isEmpty')) {
        $('#createMessage').val('');
    } else if (empty) {
        displayErrorMessage(
            'Message field is not contain only white space');
        processingBtn('#addNewForm', '#btnSave', 'reset');
        return false;
    }

    $.ajax({
        url: route('announcements.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addModal').modal('hide');
                tableName.DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addNewForm', '#btnSave');
        },
    });
});

$(document).on('click', '.edit-btn', function (event) {
    let announcementId = $(event.currentTarget).data('id');
    renderData(announcementId);
});

window.renderData = function (id) {
    $.ajax({
        url: route('announcements.edit', id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#announcementId').val(result.data.id);
                let element = document.createElement('textarea');
                element.innerHTML = result.data.subject;
                $('#editSubject').val(element.value);
                $('#editAnnouncementDate').
                    val(moment(result.data.date).
                        utc().
                        format('YYYY-MM-DD HH:mm:ss'));
                $('#editMessage').summernote('code', result.data.message);
                if (result.data.show_to_clients === true)
                    $('#editShowToClients').prop('checked', true);
                else
                    $('#editShowToClients').prop('checked', false);

                if (result.data.status === true)
                    $('#editStatus').prop('checked', true);
                else
                    $('#editStatus').prop('checked', false);
                $('#editModal').appendTo('body').modal('show');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
};

$(document).on('submit', '#editForm', function (event) {
    event.preventDefault();
    processingBtn('#editForm', '#btnEditSave', 'loading');
    let id = $('#announcementId').val();

    let $editDescription = $('<div />').
        html($('#editMessage').summernote('code'));
    let empty = $editDescription.text().trim().replace(/ \r\n\t/g, '') === '';

    if ($('#editMessage').summernote('isEmpty')) {
        $('#editMessage').val('');
    } else if (empty) {
        displayErrorMessage('Message field is not contain only white space');
        processingBtn('#editForm', '#btnEditSave', 'reset');
        return false;
    }

    $.ajax({
        url: route('announcements.update', id),
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editModal').modal('hide');
                tableName.DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#editForm', '#btnEditSave');
        },
    });
});

$(document).on('click', '.delete-btn', function () {
    let announcementId = $(this).attr('data-id');
    deleteItem(route('announcements.destroy', announcementId),
        '#announcementTable',
        Lang.get('messages.common.announcement'));
});

$('#addModal').on('show.bs.modal', function () {
    $('.note-toolbar-wrapper').removeAttr('style');
    $('.note-toolbar').removeAttr('style');
});

$('#editModal').on('show.bs.modal', function () {
    $('.note-toolbar-wrapper').removeAttr('style');
    $('.note-toolbar').removeAttr('style');
});

$('#addModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewForm', '#validationErrorsBox');
    $('#createMessage').summernote('code', '');
});

$('#editModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

$(document).on('change', '#showToClient', function () {
    let announcementId = $(this).attr('data-id')
    activeDeActiveClient(announcementId)
})

window.activeDeActiveClient = function (id) {
    $.ajax({
        url: route('announcement.active.deactive.client', id),
        method: 'post',
        cache: false,
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                tableName.DataTable().ajax.reload(null, false)
            }
        },
        complete: function () {
            stopLoader();
        },
    });
};

// Change status
$(document).on('change', '#announcementStatus', function () {
    let announcementId = $(this).attr('data-id');
    $.ajax({
        url: route('announcements.status.change', announcementId),
        method: 'post',
        cache: false,
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                tableName.DataTable().ajax.reload(null, false);
            }
        },
        complete: function () {
            stopLoader();
        },
    });
});

$(document).ready(function () {
    $('#filterAnnouncementStatus').select2();

    $('#showToClientId').select2({
        width: '190px',
    });
});
