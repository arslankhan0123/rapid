'use strict';

$(document).on('submit', '#addNewDepartmentForm', function (e) {
    e.preventDefault();
    processingBtn('#addNewDepartmentForm', '#btnSave', 'loading');
    $.ajax({
        url: route('departments.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addDepartmentModal').modal('hide');
                window.livewire.emit('refresh');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addNewDepartmentForm', '#btnSave');
        },
    });
});

$(document).on('click', '.edit-btn', function (event) {
    let departmentId = $(event.currentTarget).data('id');
    renderData(departmentId);
});

window.renderData = function (id) {
    $.ajax({
        url: route('departments.edit', id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#departmentId').val(result.data.id);
                let element = document.createElement('textarea');
                element.innerHTML = result.data.name;
                $('#editName').val(element.value);
                $('#editDepartmentModal').appendTo('body').modal('show');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
};

$(document).on('submit', '#editDepartmentForm', function (event) {
    event.preventDefault();
    processingBtn('#editDepartmentForm', '#btnEditSave', 'loading');
    let id = $('#departmentId').val();
    $.ajax({
        url: route('departments.update', id),
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editDepartmentModal').modal('hide');
                window.livewire.emit('refresh');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#editDepartmentForm', '#btnEditSave');
        },
    });
});

$(document).on('click', '.delete-btn', function () {
    let departmentId = $(this).attr('data-id');
    deleteItemLiveWire(route('departments.destroy', departmentId),
        Lang.get('messages.common.department'));
});

$('#addDepartmentModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewDepartmentForm', '#validationErrorsBox');
});

$('#editDepartmentModal').on('hidden.bs.modal', function () {
    resetModalForm('#editDepartmentForm', '#editValidationErrorsBox');
});
