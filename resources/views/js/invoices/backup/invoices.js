'use strict';



$(document).ready(function () {

    let submit;

    alert("works fine");
    $('#invoiceCurrencyId').select2({
        width: '100%',
        placeholder: Lang.get('messages.placeholder.select_currency'),
    });

    $('#discountTypeSelect').select2({
        width: '100%',
        placeholder: Lang.get('messages.placeholder.select_discount_type'),
    });

    $('#salesAgentId').select2({
        width: '100%',
        placeholder: Lang.get('messages.placeholder.select_sale_agent'),
    });

    $('.invoiceDate').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        sideBySide: true,
        widgetPositioning: {
            horizontal: 'right',
            vertical: 'bottom',
        },
        maxDate: new Date(),
        icons: {
            next: 'fa fa-chevron-right',
            previous: 'fa fa-chevron-left',
        },
    });

    $('.invoiceDueDate').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        sideBySide: true,
        widgetPositioning: {
            horizontal: 'right',
            vertical: 'bottom',
        },
        icons: {
            next: 'fa fa-chevron-right',
            previous: 'fa fa-chevron-left',
        },
    });

    $('.invoiceDate, .invoiceDueDate').on('dp.show', function () {
        matchWindowScreenPixels(
            { invoiceDate: '.invoiceDate', invoiceDueDate: '.invoiceDueDate' },
            'exp');
    });

    setTimeout(function () {
        if (editData == true && $('.invoiceDueDate').val() !== '') {
            $('.invoiceDueDate').
                data('DateTimePicker').
                minDate($('.invoiceDueDate').val());
        } else {
            $('.invoiceDueDate').data('DateTimePicker').minDate(new Date());
        }
    }, 1000);

    $('#addItemSelectBox').on('select2:select', function () {
        let id = $(this).val()

        if (isEmpty(id)) {
            return false
        }

        $.ajax({
            url: route('products.edit', id),
            type: 'GET',
            success: function (result) {
                let item = $('.items-container>tr:last-child').
                    find('.item-name').
                    val()
                if (item !== '') {
                    $('#itemAddBtn').trigger('click')
                }
                let lastItem = $('.items-container>tr:last-child');
                let element = document.createElement('textarea');
                element.innerHTML = result.data.title;
                lastItem.find('.item-name').val(element.value);
                lastItem.find('.item-description').
                    val($(result.data.description).text());
                lastItem.find('.qty').val('1').trigger('keyup');
                lastItem.find('.rate').val(result.data.rate).trigger('keyup');

                let invoiceItemTaxes = [];
                if (result.data.first_tax) {
                    invoiceItemTaxes.push(result.data.first_tax.id);
                }

                if (result.data.second_tax) {
                    invoiceItemTaxes.push(result.data.second_tax.id);
                }

                lastItem.find('.tax-rates').
                    val(invoiceItemTaxes).
                    trigger('change');
            },
        });
    });

    /** Invoice Submit */
    $(document).on('click', '#saveAsDraft, #saveAndSend', function (e) {
        e.preventDefault();
        if (submit) return false;
        submit = true;
        let paymentStatus = $(this).data('status');

        let myForm = document.getElementById('invoiceForm');
        let formData = new FormData(myForm);

        formData.append('payment_status', paymentStatus);

        let index = 0;
        let title, desc, qty, rate, amount, totalAmount;
        let itemTaxes = [];
        totalAmount = $('.total-numbers').text();
        $('.items-container>tr').each(function () {
            itemTaxes = [];
            title = $(this).find('.item-name').val();
            desc = $(this).find('.item-description').val();
            qty = $(this).find('.qty').val();
            rate = $(this).find('.rate').val();
            amount = $(this).find('.item-amount').text();
            $.each($($(this).find('.tax-rates option:selected')), function () {
                itemTaxes.push($(this).val());
            });

            formData.append('itemsArr[' + index + '][item]', title);
            formData.append('itemsArr[' + index + '][description]', desc);
            formData.append('itemsArr[' + index + '][quantity]', qty);
            formData.append('itemsArr[' + index + '][rate]', rate);
            formData.append('itemsArr[' + index + '][total]', amount);
            formData.append('itemsArr[' + index + '][tax]', itemTaxes);
            index++;
        });

        let taxValue, taxAmount;
        $('#taxesListTable>tr').each(function () {
            taxValue = $(this).find('.tax-value').text();
            taxValue = taxValue.replace('%', '');
            taxAmount = $(this).find('.footer-tax-numbers').text();
            formData.append('taxes[' + taxValue + ']', taxAmount);
        });

        formData.append('total_amount', totalAmount);
        formData.append('sub_total', $('#subTotal').text());

        $.ajax({
            url: route('invoices.store'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                startLoader();
            },
            success: function (result) {
                if (result.success) {
                    let invoiceId = result.data.id;
                    window.location = invoiceUrl + '/' + invoiceId;
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
                submit = false;
            },
            complete: function () {
                stopLoader();
            },
        });

    });

    /** Invoice Submit */
    $(document).on('click', '#editSaveSend', function (e) {
        e.preventDefault();
        let paymentStatus = $(this).data('status');

        if ($('#editAdminNote').summernote('isEmpty')) {
            $('#editAdminNote').val('');
        }

        if ($('#editClientNote').summernote('isEmpty')) {
            $('#editClientNote').val('');
        }

        if ($('#editTermAndConditions').summernote('isEmpty')) {
            $('#editTermAndConditions').val('');
        }

        let formEdit = document.getElementById('editInvoiceForm');
        let formData = new FormData(formEdit);

        formData.append('payment_status', paymentStatus);

        let index = 0;
        let title, desc, qty, rate, amount, totalAmount;
        let itemTaxes = [];
        totalAmount = $('.total-numbers').text();
        $('.items-container>tr').each(function () {
            itemTaxes = [];
            title = $(this).find('.item-name').val();
            desc = $(this).find('.item-description').val();
            qty = $(this).find('.qty').val();
            rate = $(this).find('.rate').val();
            amount = $(this).find('.item-amount').text();
            $.each($($(this).find('.tax-rates option:selected')), function () {
                itemTaxes.push($(this).val());
            });

            formData.append('itemsArr[' + index + '][item]', title);
            formData.append('itemsArr[' + index + '][description]', desc);
            formData.append('itemsArr[' + index + '][quantity]', qty);
            formData.append('itemsArr[' + index + '][rate]', rate);
            formData.append('itemsArr[' + index + '][total]', amount);
            formData.append('itemsArr[' + index + '][tax]', itemTaxes);
            index++;
        });

        let taxValue, taxAmount;
        $('#taxesListTable>tr').each(function () {
            taxValue = $(this).find('.tax-value').text();
            taxValue = taxValue.replace('%', '');
            taxAmount = $(this).find('.footer-tax-numbers').text();
            formData.append('taxes[' + taxValue + ']', taxAmount);
        });

        formData.append('total_amount', totalAmount);
        formData.append('sub_total', $('#subTotal').text());
        let id = $('#hdnInvoiceId').val();

        $.ajax({
            url: invoiceEditURL + '/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                startLoader();
            },
            success: function (result) {
                if (result.success) {
                    let invoiceId = result.data.id;
                    window.location.href = invoiceEditURL + '/' + invoiceId;
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
            complete: function () {
                stopLoader();
            },
        });

    });

    // =============================== start edit js ==============================

    if (typeof (invoiceEdit) !== 'undefined' && invoiceEdit) {
        $('.tax-rates').trigger('change');
        $('.qty').trigger('keyup');
        $('#adjustment').trigger('keyup');
        window.calculateSubTotal();
        $('#invoiceNumber').attr('readonly', true);
    }

    //================================== end edit js =====================================

    //==================================  edit js Bill And Ship Address =====================================

    $('.address-modal').on('show.bs.modal', function () {
        setTimeout(function () {
            $('#billStreet').focus();
        }, 500);
    });

    window.checkBillAddressFields = function () {
        return $('#billStreet,#billCity,#billState,#billZipCode,#billCountry').
            filter(function () {
                return this.value != '';
            });
    };

    window.showBillingShippingAddressError = function (selector) {
        displayErrorMessage($(selector).data('err-msg'));
    };

    let shippingData = '';
    $(document).on('change', '#shippingAddressEnable', function () {
        if (!$(this).prop('checked') &&
            (typeof createData !== 'undefined' || typeof editData !==
                'undefined')) {
            $('#shipStreet').val('');
            $('#copyBillingAddress').prop('checked', false);
            $('#shipStreet,#shipCity,#shipState,#shipZipCode,#shipCountry').val('');
        }
    });

    window.checkStreetField = function () {
        if ($.trim($('#billStreet').val()) === '') {
            showBillingShippingAddressError('#billStreet');
            return false;
        } else if ($('#shippingAddressEnable').prop('checked') &&
            $.trim($('#shipStreet').val()) === '') {
            showBillingShippingAddressError('#shipStreet');
            return false;
        }

        return true;
    };

    let billingAddressForm = $('#addressForm');
    let shippingAddressForm = $('#shippingAddressForm');

    if (typeof editInvoiceAddress !== 'undefined' && editInvoiceAddress) {
        if (checkBillAddressFields().length == 0) {
            $('#bill_to').html('_ _ _ _ _ _');
        } else {
            $('#bill_to').html(getAddressDetail(billingAddressForm));
        }

        if ($('#shipStreet').val() !== '') {
            $('#ship_to').html(getAddressDetail(shippingAddressForm));
        } else {
            $('#ship_to').html('_ _ _ _ _ _');
        }
    }

    $(document).on('click', '#btnSaveAddress', function () {

        if (!checkStreetField()) {
            return false;
        }

        // let billZipCode = checkZipcode($('#billZipCode').val());
        // if (!billZipCode) {
        //     return false;
        // }
        // if ($('#shippingAddressEnable').prop('checked')) {
        //     let shipZipCodeValue = $('#shipZipCode').val();
        //     let shipZipCode = checkZipcode(shipZipCodeValue);
        //     if (!shipZipCode) {
        //         return false;
        //     }
        // }

        if (typeof createInvoiceAddress !== 'undefined' &&
            createInvoiceAddress) {
            if (checkBillAddressFields().length == 0) {
                $('#bill_to').html('_ _ _ _ _ _');
            } else {
                $('#bill_to').html(createAddressDetail(billingAddressForm));
            }

            if ($('#shippingAddressEnable').prop('checked') == true &&
                $('#shipStreet').val() != '') {
                $('#ship_to').html(createAddressDetail(shippingAddressForm));
            } else {
                $('#ship_to').html('_ _ _ _ _ _');
            }
        }

        if (typeof editInvoiceAddress !== 'undefined' && editInvoiceAddress) {
            if (checkBillAddressFields().length == 0) {
                $('#bill_to').html('_ _ _ _ _ _');
            } else {
                $('#bill_to').html(getAddressDetail(billingAddressForm));
            }

            if ($('#shippingAddressEnable').prop('checked') == true &&
                $('#shipStreet').val() != '') {
                $('#ship_to').html(getAddressDetail(shippingAddressForm));
            } else {
                $('#ship_to').html('_ _ _ _ _ _');
            }
        }

        $('.address-modal').modal('hide');
    });

    if (typeof editData !== 'undefined') {
        if ($.trim($('#shipStreet').val()) !== '') {
            $('#shippingAddressEnable').prop('checked', true);
            $('#shippingAddressForm').slideDown();
            shippingData = $('#shipStreet').val();
        }
    }

    if ($('#customerSelectBox').val() !== '') {
        setTimeout(function () {
            $('#customerSelectBox').trigger('change');
        }, 100);
    }

    $(document).on('change', '#customerSelectBox', function () {
        let customer_id = $(this).val();
        $('#shippingAddressEnable').prop('checked', false);
        $('#shippingAddressForm').slideUp();
        $('#copyBillingAddress').prop('checked', false);
        $('#billStreet,#billCity,#billState,#billZipCode,#billCountry').val('');
        $('#shipStreet,#shipCity,#shipState,#shipZipCode,#shipCountry').val('');
        $.ajax({
            url: customerURL,
            type: 'GET',
            data: { customer_id: customer_id },
            success: function (result) {
                $("#bill_to").empty();
                $('#ship_to').empty();
                let billAddress = result.data[0];
                let shipAddress = result.data[1];
                $('#copyBillingAddress').prop('checked', true);

                if(! isEmpty(billAddress)) {
                    $("#bill_to").append("<span>" + billAddress.street + '</span><br>');
                    $("#bill_to").append("<span>" + billAddress.city + ',</span>');
                    $("#bill_to").append("<span>" + billAddress.state + '</span><br>');
                    $("#bill_to").append("<span>" + billAddress.country + '-</span>');
                    $("#bill_to").append("<span>" + billAddress.zip + '</span>');

                    $('#billStreet').val(result.data[0].street);
                    $('#billCity').val(result.data[0].city);
                    $('#billState').val(result.data[0].state);
                    $('#billZipCode').val(result.data[0].zip);
                    $('#billCountry').val(result.data[0].country);
                }else{
                    $('#bill_to').html('_ _ _ _ _ _');
                    $('#ship_to').html('_ _ _ _ _ _');
                }
                if(! isEmpty(shipAddress)) {
                    $('#shippingAddressEnable').prop('checked', true);
                    $('#shippingAddressForm').slideDown();
                    $('#copyBillingAddress').prop('checked', true);

                    $('#ship_to').
                        append(
                            '<span>' + shipAddress.street + '</span><br>');
                    $('#ship_to').
                        append('<span>' + shipAddress.city + ',</span>');
                    $('#ship_to').
                        append(
                            '<span>' + shipAddress.state + '</span><br>');
                    $('#ship_to').
                        append('<span>' + shipAddress.country + '-</span>');
                    $('#ship_to').
                        append('<span>' + shipAddress.zip + '</span>');

                    $('#shipStreet').val(result.data[1].street);
                    $('#shipCity').val(result.data[1].city);
                    $('#shipState').val(result.data[1].state);
                    $('#shipZipCode').val(result.data[1].zip);
                    $('#shipCountry').val(result.data[1].country);
                }else{
                    $('#shippingAddressEnable').prop('checked',false);
                    $('#shippingAddressForm').slideUp();
                    $('#copyBillingAddress').prop('checked',false);
                    $('#ship_to').html('_ _ _ _ _ _');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });
    });

    $(document).on('click', '#copyBillingAddress', function () {
        if ($(this).prop('checked')) {
            $('#shipStreet').val($('#billStreet').val());
            $('#shipCity').val($('#billCity').val());
            $('#shipState').val($('#billState').val());
            $('#shipZipCode').val($('#billZipCode').val());
            $('#shipCountry').val($('#billCountry').val());
        } else {
            $('#shipStreet,#shipCity,#shipState,#shipZipCode,#shipCountry').
                val('');
        }
    });
});
