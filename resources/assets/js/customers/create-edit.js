'use strict';

let customerUrl = route('customers.index');

$(document).ready(function () {
    $('#groupId').select2({
        width: 'calc(100% - 44px)',
        placeholder: Lang.get('messages.placeholder.select_groups'),
        multiple: true,
    });

    $('#customerId').select2({
        width: '200px',
    });

    $('#currencyId,#countryId,#languageId,#billingCountryId,#shippingCountryId').
        select2({
            width: '100%',
        });

    $(document).on('click', '.addressModalIcon', function () {
        $('#addModal').appendTo('body').modal('show');
    });

    if ($('#billingStreet').val() !== '' &&
        $('#billingCity').val() !== '' &&
        $('#billingState').val() !== '' &&
        $('#billingZip').val() !== '') {
        $('#shippingAddressCheck').removeAttr('disabled');
    }

    $(document).
        on('keypress keyup',
            '#billingStreet, #billingCity, #billingState, #billingZip',
            function () {
                if ($('#billingStreet').val() === '' &&
                    $('#billingCity').val() === '' &&
                    $('#billingState').val() === '' &&
                    $('#billingZip').val() === '') {
                    $('#shippingAddressCheck').attr('disabled', true);
                } else {
                    $('#shippingAddressCheck').removeAttr('disabled');
                }
            });

    $(document).on('click', '#copyBillingAddress', function () {
        if ($('#shippingAddressCheck').prop('checked') === true) {
            $('#shippingStreet').val($('#billingStreet').val());
            $('#shippingCity').val($('#billingCity').val());
            $('#shippingState').val($('#billingState').val());
            $('#shippingZip').val($('#billingZip').val());
            $('#shippingCountryId').
                val($('#billingCountryId').val()).
                trigger('change.select2');
        } else {
            $('#shippingStreet').val('');
            $('#shippingCity').val('');
            $('#shippingState').val('');
            $('#shippingZip').val('');
            $('#shippingCountryId').
                val('').
                trigger('change.select2');
        }
    });

    $(document).on('change', '#customerId', function () {
        let urlLastString = window.location.href.substring(
            window.location.href.lastIndexOf('/') + 1)
        location.href = !isNaN(urlLastString) ? customerUrl + '/' +
            $(this).val() +
            '/profile' : customerUrl + '/' + $(this).val() + '/' +
            urlLastString
    });

    $(document).on('submit', '#createCustomer, #editCustomer', function () {
        // let shipZipCode = checkZipcode($('#shippingZip').val());
        // if (!shipZipCode) {
        //     return false;
        // }

        if (isEmpty($('#company_name').val())) {
            displayErrorMessage('The Company Name field is required.');
            return false;
        }

        if ($('#error-msg').text() !== '') {
            $('#phoneNumber').focus();
            return false;
        }

        let loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');

        $('#btnSave').prop('disabled', true);
    });

    $(document).on('submit', '#addressForm', function (event) {
        event.preventDefault();
        let customerId = $('#customer_id').val();
        $.ajax({
            url: route('add.customer.address'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    $('#addModal').modal('hide');
                    location.reload();
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

    $(document).on('blur', '#website', function () {
        var website = $(this).val();
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

    $('.address-modal').on('hidden.bs.modal', function () {
        $('#shippingAddressCheck').prop('checked', false);
        $('#billingCountryId,#shippingCountryId').val('').trigger('change');
        resetModalForm('#addressForm', '#validationErrorsBox');
    });

    $(document).on('submit', '#addCustomerGroupForm', function (event) {
        event.preventDefault();
        processingBtn('#addCustomerGroupForm', '#btnSave', 'loading');

        $.ajax({
            url: route('customer-groups.store'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    $('#customerGroupModal').modal('hide');
                    displaySuccessMessage(result.message);
                    let data = {
                        id: result.data.id,
                        name: result.data.name,
                    };

                    let newOption = new Option(data.name, data.id, false, true);
                    $('#groupId').append(newOption).trigger('change');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
            complete: function () {
                processingBtn('#addCustomerGroupForm', '#btnSave');
            },
        });
    });

    $('#customerGroupModal').on('hidden.bs.modal', function () {
        resetModalForm('#addCustomerGroupForm', '#validationErrorsBox');
    });

});
