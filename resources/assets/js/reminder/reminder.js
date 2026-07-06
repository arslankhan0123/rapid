'use strict';

$(document).ready(function () {
    $('#reminderTo,#editReminderTo').select2({
        width: '100%',
        placeholder: Lang.get('messages.reminder.reminder_to'),
    })

    $('.notified-date, .edit-notified-date').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: true,
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
    }).on('dp.show', function () {
        matchWindowScreenPixels(
            {
                notifiedDate: '.notified-date',
                editNotifiedDate: '.edit-notified-date',
            },
            'led')
    })
});

let tableName = '#reminderTbl';
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
    'order': [[1, 'desc']],
    ajax: {
        url: route('reminder.index'),
        beforeSend: function () {
            startLoader();
        },
        data: function (data) {
            data.module_id = $('#moduleId').val();
            data.owner_id = $('#ownerId').val();
        },
        complete: function () {
            stopLoader();
        },
    },
    columnDefs: [
        {
            'targets': [4],
            'orderable': false,
            'className': 'text-center',
            'width': '8%',
        },
        {
            'targets': [3],
            'className': 'text-center',
            'orderable': false,
            'width': '4%',
        },
        {
            'targets': [0],
            'orderable': false,
            'className': 'text-center',
            'width': '11%',
        },
        {
            'targets': [1],
            'width': '18%',
        },
        {
            targets: '_all',
            defaultContent: 'N/A',
        },
    ],
    columns: [
        {
            data: function (row) {
                if (row.owner_type == 'App\\Models\\Customer') {
                    let element = document.createElement('textarea');
                    element.innerHTML = row.user.full_name;
                    return '<a href="' + route('contacts.index') + '/' +
                        row.user.owner_id +
                        '"><img src="' + row.user.image_url +
                        '" class="thumbnail-rounded" data-toggle="tooltip" title="' +
                        element.value + '"></a>';
                }
                let element = document.createElement('textarea');
                element.innerHTML = row.user.full_name;
                return '<a href="' + route('members.index') + '/' +
                    row.user.id +
                    '"><img src="' + row.user.image_url +
                    '" class="thumbnail-rounded" data-toggle="tooltip" title="' +
                    element.value + '"></a>';
            },
            name: 'reminder_to',
        },
        {
            data: function (row) {
                return row;
            },
            render: function (row) {
                if (row.notified_date === null) {
                    return 'N/A'
                }
                return moment(row.notified_date).
                    locale(currentLocale).
                    format('Do MMM, Y h:mm A')
            },
            name: 'notified_date',
        },
        {
            data: function (row) {
                let element = document.createElement('textarea');
                element.innerHTML = row.description;
                return element.value;
            },
            name: 'description',
        },
        {
            data: function (row) {
                if (row.status) {
                    return Lang.get('messages.reminder.yes')   
                }
                return Lang.get('messages.reminder.no')
            },
            name: 'status',
        },
        {
            data: function (row) {
                let data = [
                    {
                        'id': row.id,
                    }];
                return prepareTemplateRender('#reminderActionTemplate',
                    data);
            }, name: 'user.last_name',
        },
    ],
    'fnInitComplete': function () {
        $(document).on('change', '#filterNotified', function () {
            $(tableName).DataTable().ajax.reload(null, true);
        });
    },
});

$(document).on('click', '.addReminderModal', function () {
    $('#reminderTo').val('').trigger('change')
    $('.notified-date').data('DateTimePicker').date(null)
    $('#addModal').appendTo('body').modal('show')
});

window.checkReminderContent = function (elementSelector) {
    const elSelector = '#' + elementSelector;
    if ($(elSelector).summernote('isEmpty')) {
        displayErrorMessage('Description field is required.');
        return false;
    }

    return true;
};

$(document).on('submit', '#addNewForm', function (e) {
    e.preventDefault();

    if (!checkReminderContent('reminderDescription'))
        return;

    processingBtn('#addNewForm', '#btnCreateSave', 'loading');

    let createDescription = $('<div />').
        html($('#reminderDescription').summernote('code'));
    let empty = createDescription.text().trim().replace(/ \r\n\t/g, '') === '';

    if ($('#reminderDescription').summernote('isEmpty')) {
        $('#reminderDescription').val('');
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space');
        processingBtn('#addNewForm', '#btnCreateSave', 'reset');
        return false;
    }

    $.ajax({
        url: route('reminder.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#addModal').modal('hide')
                $('.notified-date').data('DateTimePicker').date(null)
                $('.notified-date').data('DateTimePicker').date(new Date())
                $('#reminderTbl').DataTable().ajax.reload(null, true)
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addNewForm', '#btnCreateSave');
        },
    });
});

$(document).on('click', '.edit-reminder-btn', function (event) {
    let reminderId = $(event.currentTarget).data('id');
    renderEditData(reminderId);
});

window.renderEditData = function (id) {
    $.ajax({
        url: route('reminder.edit', id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#reminderId').val(result.data.id);
                $('#editNotifiedDate').
                    val(format(result.data.notified_date,
                        'YYYY-MM-DD HH:mm:ss'));
                $('#editReminderTo').
                    val(result.data.reminder_to).
                    trigger('change');
                $('#editReminderDescription').
                    summernote('code', result.data.description);
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

    if (!checkReminderContent('editReminderDescription'))
        return;

    processingBtn('#editForm', '#btnEditSave', 'loading');
    let id = $('#reminderId').val();

    let editDescription = $('<div />').
        html($('#editReminderDescription').summernote('code'));
    let empty = editDescription.text().trim().replace(/ \r\n\t/g, '') === '';

    if ($('#editReminderDescription').summernote('isEmpty')) {
        $('#editReminderDescription').val('');
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space');
        processingBtn('#editForm', '#btnEditSave', 'reset');
        return false;
    }

    $.ajax({
        url: route('reminder.update', id),
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editModal').modal('hide');
                $('#reminderTbl').DataTable().ajax.reload(null, true);
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

$(document).on('click', '.delete-reminder-btn', function (event) {
    let reminderId = $(event.currentTarget).data('id');
    deleteItem(route('reminder.destroy', reminderId), '#reminderTbl',
        Lang.get('messages.ticket.reminder'));
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
    resetModalForm('#addNewForm', '#validationErrorsBox')
    $('#reminderDescription').summernote('code', '')
    $('.notified-date').val('')
    $('.notified-date').data('DateTimePicker').date(null)
    $('.notified-date').data('DateTimePicker').date(new Date())
});

$('#editModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

$(tableName).on('draw.dt', function () {
    $('.tooltip').tooltip('hide');
    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
