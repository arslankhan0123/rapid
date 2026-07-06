'use strict';

$(document).ready(function () {
    $('#leadTagID').select2({
        width: 'calc(100% - 44px)',
        placeholder: Lang.get('messages.placeholder.select_tags'),
    })

    $('#memberId,#countryId,#languageId').select2({
        width: '100%',
    });
    
    $('#sourceId').select2({
        width: 'calc(100% - 44px)',
        placeholder: Lang.get('messages.placeholder.select_source'),
    });

    $('#statusId').select2({
        width: 'calc(100% - 44px)',
        placeholder: Lang.get('messages.placeholder.select_status'),
    });

    window.toggleDateField = function (selector) {
        if ($(selector).prop('checked') === true) {
            $('#contactForm').slideUp();
        } else {
            $('#contactForm').slideDown();
        }
    };

    $(document).on('change', '#checkContact', function () {
        toggleDateField('#checkContact');
    });

    if (typeof isEdit !== 'undefined' && isEdit) {
        toggleDateField('#checkContact');
    }

    $('#contactDateId').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: true,
        sideBySide: true,
        icons: {
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            next: 'fa fa-chevron-right',
            previous: 'fa fa-chevron-left',
        },
    });

    $('#leadDescription').summernote({
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });

    $(document).on('submit', '#createLead, #editLead', function () {
        let loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');

        let description = $('<div />').
            html($('#leadDescription').summernote('code'));
        let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

        if ($('#leadDescription').summernote('isEmpty')) {
            $('#leadDescription').val('');
        } else if (empty) {
            displayErrorMessage(
                'Description field is not contain only white space');
            let loadingButton = jQuery(this).find('#btnSave');
            loadingButton.button('reset');
            return false;
        }

        if ($('#error-msg').text() !== '') {
            $('#phoneNumber').focus();
            return false;
        }
    });

    $(document).on('blur', '#website', function () {
        let website = $(this).val();

        if (isEmpty(website)) {
            $('#website').val('');
        } else {
            website = websiteURLConvert(website);
            $('#website').val(website);
        }
    });

    window.websiteURLConvert = function (website) {
        if (!~website.indexOf('http')) {
            website = 'http://' + website;
        }

        return website;
    };

});

$(document).on('submit', '#addNewForm', function (e) {
    e.preventDefault();
    processingBtn('#addNewForm', '#btnSave', 'loading');
    $.ajax({
        url: route('lead.source.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addModal').modal('hide');
                let data = {
                    id: result.data.id,
                    name: result.data.name,
                };

                let newOption = new Option(data.name, data.id, false, true);
                $('#sourceId').append(newOption).trigger('change');
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

$('#addModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewForm', '#validationErrorsBox');
});

$(document).on('submit', '#addLeadStatusForm', function (e) {
    e.preventDefault();
    processingBtn('#addLeadStatusForm', '#btnSave', 'loading');

    $.ajax({
        url: route('lead.status.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addLeadStatusModal').modal('hide');
                let data = {
                    id: result.data.id,
                    name: result.data.name,
                };

                let newOption = new Option(data.name, data.id, false, true);
                $('#statusId').append(newOption).trigger('change');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addLeadStatusForm', '#btnSave');
        },
    });
});

$('#addLeadStatusModal').on('hidden.bs.modal', function () {
    resetModalForm('#addLeadStatusForm', '#validationErrorsBox');
});
