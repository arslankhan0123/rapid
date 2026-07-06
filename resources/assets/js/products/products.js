'use strict';

$(document).ready(function () {

    $('#filter_group').
        select2({
            width: '200px',
        });

    $('#taxSelectOne, #editTaxSelectOne').
        select2({
            width: '100%',
        });

    $('#taxSelectTwo,#editTaxSelectTwo').
        select2({
            width: '100%',
        });

    $('#productGroup, #editProductGroup').select2({
        width: 'calc(100% - 44px)',
        placeholder:Lang.get('messages.placeholder.select_product_group') ,
    });

    $('#productDescription, #editProductDescription').summernote({
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });
});

let tableName = $('#productsTable');
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
        url: route('products.index'),
        beforeSend: function () {
            startLoader();
        },
        data: function (data) {
            data.group = $('#filter_group').
                find('option:selected').
                val();
        },
        complete: function () {
            stopLoader();
        },
    },
    columnDefs: [
        {
            'targets': [6],
            'orderable': false,
            'className': 'text-center',
            'width': '50px',
        },
        {
            'targets': [2],
            render: function (data) {
                return data.length > 80 ?
                    data.substr(0, 80) + '...' :
                    data;
            },
        },
        {
            'targets': [3, 4, 5],
            'className': 'text-right',
        },
        {
            targets: '_all',
            defaultContent: 'N/A',
        },
    ],
    columns: [
        {
            data: function (row) {
                let element = document.createElement('textarea');
                element.innerHTML = row.title;
                return element.value;
            },
            name: 'title',
        },
        {
            data: function (row) {
                let element = document.createElement('textarea');
                element.innerHTML = row.group.name;
                return element.value;
            },
            name: 'item_group_id',
        },
        {
            data: function (row) {
                let element = document.createElement('textarea');
                element.innerHTML = row.description;
                if (element.value != '')
                    return element.value;
                else
                    return 'N/A';
            },
            name: 'description',
        },
        {
            data: function (row) {
                if (row.rate != 0) {
                    return '<i class="' + currentCurrencyClass + '"></i>' +
                        ' ' +
                        getFormattedPrice(row.rate);
                }
                return '<i class="' + currentCurrencyClass + '"></i>' + ' ' + 0;
            },
            name: 'rate',
        },
        {
            data: 'first_tax.tax_rate',
            name: 'firstTax.tax_rate',
        },
        {
            data: 'second_tax.tax_rate',
            name: 'firstTax.tax_rate',
        },
        {
            data: function (row) {
                let data = [{ 'id': row.id }];
                return prepareTemplateRender('#productActionTemplate', data);
            }, name: 'id',
        },
    ],
    'fnInitComplete': function () {
        $(document).on('change', '#filter_group', function () {
            tableName.DataTable().ajax.reload(null, true);
        });
    },
});

$(document).on('submit', '#addNewForm', function (e) {
    e.preventDefault();
    processingBtn('#addNewForm', '#btnSave', 'loading');

    let productDescription = $('<div />').
        html($('#productDescription').summernote('code'));
    let empty = productDescription.text().trim().replace(/ \r\n\t/g, '') === '';

    if ($('#productDescription').summernote('isEmpty')) {
        $('#productDescription').val('');
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space');
        processingBtn('#addNewForm', '#btnSave', 'reset');
        return false;
    }

    $.ajax({
        url: route('products.store'),
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
    let productId = $(event.currentTarget).data('id');
    renderData(productId);
});

window.renderData = function (id) {
    $.ajax({
        url: route('products.edit', id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#productId').val(result.data.id);
                let element = document.createElement('textarea');
                element.innerHTML = result.data.title;
                $('#editTitle').val(element.value);
                $('#editProductDescription').
                    summernote('code', result.data.description);
                $('#editRate').val(result.data.rate);
                $('.price-input').trigger('input');
                $('#editTaxSelectOne').
                    val(result.data.tax_1_id).
                    trigger('change');
                $('#editTaxSelectTwo').
                    val(result.data.tax_2_id).
                    trigger('change');
                $('#editProductGroup').
                    val(result.data.item_group_id).
                    trigger('change');
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
    let id = $('#productId').val();

    let editProductDescription = $('<div />').
        html($('#editProductDescription').summernote('code'));
    let empty = editProductDescription.text().trim().replace(/ \r\n\t/g, '') ===
        '';

    if ($('#editProductDescription').summernote('isEmpty')) {
        $('#editProductDescription').val('');
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space');
        processingBtn('#editForm', '#btnEditSave', 'reset');
        return false;
    }

    $.ajax({
        url: route('products.update', id),
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

$(document).on('click', '.delete-btn', function (event) {
    let productId = $(event.currentTarget).data('id');
    deleteItem(route('products.destroy', productId), '#productsTable',
        Lang.get('messages.common.product_1'));
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
    $('#productGroup').val('').trigger('change');
    $('#taxSelectOne').val('').trigger('change');
    $('#taxSelectTwo').val('').trigger('change');
    $('#productDescription').summernote('code', '');
});

$('#editModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

$(document).on('submit', '#addProductGroupForm', function (e) {
    e.preventDefault();
    processingBtn('#addProductGroupForm', '#btnSave', 'loading');

    $.ajax({
        url: route('product-groups.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addProductGroupModal').modal('hide');
                let data = {
                    id: result.data.id,
                    name: result.data.name,
                };

                let newOption = new Option(data.name, data.id, false, true);
                $('#productGroup').append(newOption).trigger('change');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addProductGroupForm', '#btnSave');
        },
    });
});

$('#addProductGroupModal').on('hidden.bs.modal', function () {
    resetModalForm('#addProductGroupForm', '#validationErrorsBox');
});

$(document).on('submit', '#editProductGroupForm', function (e) {
    e.preventDefault();
    processingBtn('#editProductGroupForm', '#btnSave', 'loading');

    $.ajax({
        url: route('product-groups.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editProductGroupModal').modal('hide');
                let data = {
                    id: result.data.id,
                    name: result.data.name,
                };

                let newOption = new Option(data.name, data.id, false, true);
                $('#editProductGroup').append(newOption).trigger('change');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#editProductGroupForm', '#btnSave');
        },
    });
});

$('#editProductGroupModal').on('hidden.bs.modal', function () {
    resetModalForm('#editProductGroupForm', '#validationErrorsBox');
});
