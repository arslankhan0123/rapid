'use strict'

let tableName = '#translatorManagerTbl'
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
    'order': [[0, 'asc']],
    ajax: {
        url: route('translation-manager.index'),
    },
    columnDefs: [
        {
            'targets': [1],
            'orderable': false,
        },
        {
            'targets': [2],
            'orderable': false,
            'className': 'text-center',
            'width': '8%',
        },
        {
            targets: '_all',
            defaultContent: 'N/A',
        },
    ],
    columns: [
        {
            data: 'name',
            name: 'name',
        },
        {
            data: function (row) {
                let editTranslatorUrl = route('language.translation', row.id)
                return `<a href="${editTranslatorUrl}" class="text-decoration-none">Edit Translation</a>`
            },
            name: 'id',
        },
        {
            data: function (row) {
                let data = [
                    {
                        'id': row.id,
                    }]
                return prepareTemplateRender('#translatorManagerActionTemplate',
                    data)
            }, name: 'id',
        },
    ],
})

$(document).on('click', '.addLanguageModal', function () {
    $('#addModal').appendTo('body').modal('show')
})

$(document).on('submit', '#addNewForm', function (e) {
    e.preventDefault()
    processingBtn('#addNewForm', '#btnSave', 'loading')
    $.ajax({
        url: route('translation-manager.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#addModal').modal('hide')
                $('#translatorManagerTbl').DataTable().ajax.reload(null, true)
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
        complete: function () {
            processingBtn('#addNewForm', '#btnSave')
        },
    })
})

$(document).on('click', '.edit-translator-btn', function (event) {
    let languageId = $(this).attr('data-id')
    renderLanguageData(languageId)
})

function renderLanguageData (id) {
    $.ajax({
        url: route('translation.manager.edit', id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#languageId').val(result.data.id)
                $('.langName').val(result.data.name)
                $('#editLanguageModal').appendTo('body').modal('show')
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
    })
}

$(document).on('submit', '#editLanguageForm', function (event) {
    event.preventDefault()
    processingBtn('#editLanguageForm', '#btnEditSave', 'loading')
    let id = $('#languageId').val()
    $.ajax({
        url: route('translation-manager.update', id),
        type: 'PUT',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#editLanguageModal').modal('hide')
                $('#translatorManagerTbl').DataTable().ajax.reload(null, true)
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
        complete: function () {
            processingBtn('#editLanguageForm', '#btnEditSave')
        },
    })
})

$(document).on('click', '.delete-translate-language', function () {
    let languageId = $(this).attr('data-id')
    deleteItem(route('translation.manager.destroy', languageId), tableName,
        'Language')
})

$('#addModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewForm', '#validationErrorsBox')
})
