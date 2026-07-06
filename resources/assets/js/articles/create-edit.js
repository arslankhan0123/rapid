'use strict';

let articleGroupCreateUrl = route('article-groups.store');

$(document).ready(function () {
    $('#groupId').select2({
        width: 'calc(100% - 44px)',
        placeholder: Lang.get('messages.placeholder.select_group'), 
    });
});

$(document).on('submit', '#createArticle, #editArticle', function () {
    let loadingButton = jQuery(this).find('#btnSave');
    loadingButton.button('loading');

    let description = $('<div />').
        html($('#articleDescription').summernote('code'));
    let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

    if ($('#articleDescription').summernote('isEmpty')) {
        $('#articleDescription').val('');
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space');
        let loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('reset');
        return false;
    }

    $('#btnSave').prop('disabled', true);
});

$(document).on('change', '#attachment', function () {
    let validFile = isValidFile($(this), '#validationErrorBox');
    if (!validFile) {
        return false;
    }
});

$(document).on('submit', '#addArticleGroupForm', function (e) {
    e.preventDefault();
    processingBtn('#addArticleGroupForm', '#btnSave', 'loading');

    $.ajax({
        url: articleGroupCreateUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addArticleGroupModal').modal('hide');
                let data = {
                    id: result.data.id,
                    name: result.data.group_name,
                };

                let newOption = new Option(data.name, data.id, false, true);
                $('#groupId').append(newOption).trigger('change');

            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addArticleGroupForm', '#btnSave');
        },
    });
});

$('#addArticleGroupModal').on('hidden.bs.modal', function () {
    resetModalForm('#addArticleGroupForm', '#validationErrorsBox');
});
