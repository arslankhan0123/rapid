@extends('layouts.app')
@section('title')
    {{ __('messages.manual-sales.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ mix('assets/css/invoices/invoices.css') }}">
    <style>
        /* For Chrome, Safari, Edge, Opera */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* For Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }

        .fa-times {
            font-size: 15px;
            color: black;
            /* Default color */
            transition: color 0.1s ease;
            /* Smooth color transition */
        }

        .fa-times:hover {
            color: red;
            /* Color on hover */
            cursor: pointer;
            /* Changes cursor to pointer for better UX */
        }

        /* Right-align text for table headers and input fields */
        th {
            text-align: right;
            /* Align header text to the right */
        }

        #itemsTable tbody tr td {

            /* Align table cell text to the right */
        }

        /* Specific input fields alignment for amounts */
        .quantity,
        .rates,
        .discount,
        .taxable,
        .tax,
        .vat-amount,
        .total-amount {
            text-align: right !important;
            /* Ensures right-alignment for input content */
        }

        .bgColor {
            background: #8887870a !important;

        }

        .table-bordered {
            border-color: white !important;
        }

        .form-group {
            margin-bottom: 0 !important;
            /* Remove bottom margin */
            padding-bottom: 0;
            /* Ensure no padding at the bottom */
        }

        .form-control,
        label {
            margin-bottom: 0 !important;
            /* Remove margin from inputs and labels */
        }

        /* Reduce the height of the Select2 dropdown */
        input,
        select,

        .form-control {
            /* height: 42px !important; */

        }

        .section-header {
            margin-top: -30px !important;
            margin-bottom: 5px !important;
            height: 55px;
        }

        .table-wrapper {
            max-height: 400px;
            /* Set the desired height for the table */
            overflow-y: auto;
            /* Enable vertical scrolling */
        }

        #itemsTable thead {
            position: sticky;
            top: 0;
            background-color: #fff;
            /* Match the table background */
            z-index: 1;
            /* Ensure it stays above other content */
        }

        #branchSelect {
            -webkit-appearance: none;
            /* Removes the dropdown arrow for Webkit browsers */
            -moz-appearance: none;
            /* Removes the dropdown arrow for Firefox */
            appearance: none;
            /* Standard property */
            background: transparent;
            /* Optional: makes the background transparent */
            padding-right: 10px;
            /* Space where the dropdown arrow would have been */
            border: none;
            /* Optional: remove borders for a cleaner look */
        }



        .select2-selection__rendered {
            width: 250px !important;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.manual-sales.add') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                {{ Form::open(['route' => 'invoices.store', 'validated' => false, 'id' => 'manualSalesForm']) }}
                @include('manual-sales.address_modal')
                @include('manual-sales.fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('manual-sales.templates.templates')
    @include('tags.common_tag_modal')
    @include('payment_modes.common_payment_mode')
    @include('manual-sales.modal_create_item')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#addItemSelectBox').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.commissions.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
        });
    </script>
@endsection
@section('scripts')
    <script>
        let taxData = JSON.parse('@json($data['taxes'])');
        let invoiceUrl = "{{ route('manual-sales.index') }}";

        let isCreate = true;
        let createData = true;
        let createInvoiceAddress = true;
        let customerURL = "{{ route('get.customer.address') }}";
        let editData = false;
        let categories = @JSON($categories);

        let customers = @JSON($customers);
        let allBranches = @json($usersBranches);

        let services = @json($services);

        let isRound = "{{ $isRound }}";
        isRound = isRound === "1" || isRound === "true";

        $('#supplierSelectBox').on('change', function() {
            let selectedText = $(this).find('option:selected').text();
            $('#customerNameInput').val(selectedText);
            // Error hide karein jab customer select ho
            $('#customerValidationError').hide();
            $('#customerNameInput').removeClass('is-invalid');
        });

        $('#customerNameInput').on('input', function() {
            if ($(this).val().trim() !== '') {
                $('#customerValidationError').hide();
                $(this).removeClass('is-invalid');
            }
        });

        // Customer validation function
        function validateCustomer() {
            let name = $('#customerNameInput').val().trim();
            let customerId = $('#supplierSelectBox').val();
            if (!name && !customerId) {
                $('#customerValidationError').show();
                $('#customerNameInput').addClass('is-invalid');
                $('#customerNameInput')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
            $('#customerValidationError').hide();
            $('#customerNameInput').removeClass('is-invalid');
            return true;
        }

        // Main Submit button validation
        $('#mainSubmitBtn').on('click', function(e) {
            if (!validateCustomer()) {
                e.preventDefault();
                return false;
            }
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
    </script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/sales/sales.js') }}"></script>


    <script>
        $(document).ready(function() {


            $('#btnSaveEstimateAddress').on('click', function(e) {
                e.preventDefault();

                // Clear previous error messages
                $('#validationErrorsBox').addClass('d-none').html('');
                let estimateId = $('#estimateNumber').val(); // Assuming this is an input field
                // Prepare the form data
                let formData = {
                    street: $('textarea[name="street[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    city: $('input[name="city[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    state: $('input[name="state[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    zip_code: $('input[name="zip_code[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    country: $('input[name="country[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    estimate_id: estimateId,


                };

                var createURL = route('estimates.address');
                $.ajax({
                    url: createURL,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response) {
                            // Format the address with commas
                            let addressText =
                                `${response.street}, ${response.city}, ${response.state} - ${response.zip_code}, ${response.country}`;

                            // Update the content of #bill_to with the formatted address
                            $('#bill_to').text(addressText);
                            // Close the modal
                            $('#addModal').modal('hide');
                            displaySuccessMessage("Address Added");

                        }
                    },
                    error: function(response) {

                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {






            // Event listener for when a customer is selected from the dropdown
            $('#customerSelectBox').on('change', function() {
                var customerId = $(this).val();
                var customerName = $('#customerSelectBox option:selected').text();
                $('#customerNameInput').val(customerName);
                var customer = customers.find(c => c.id == customerId);
                $('#vendor_code').val(customer?.vendor_code ?? '');
            });


            $('#project_id').on('change', function() {
                let selectedProjectName = $('#project_id option:selected').text();
                if (!$(this).val()) {
                    $('#project_name').val('');
                } else {
                    $('#project_name').val(selectedProjectName);
                }
            });
            $('#project_id').trigger('change');



        });

        adjustHeights();

        // Adjust heights on window resize
        $(window).resize(function() {
            adjustHeights();
        });


        function adjustHeights() {
            // Get the window height
            const windowHeight = $(window).height();

            // Calculate heights based on percentages
            const headerHeight = (18.5 / 100) * windowHeight;
            const contentHeight = (30 / 100) * windowHeight;
            const footerHeight = (15 / 100) * windowHeight;

            $('.page_contents').css({
                'height': `${contentHeight}px`,
                overflow: 'auto', // Add scrollbar if overflow
            });
        }
    </script>


    <script>
        function strip_tags(str) {
            if ((str === null) || (str === '')) return '';
            else str = str.toString();
            return str.replace(/<\/?[^>]+(>|$)/g, ""); // Remove HTML tags
        }
    </script>


    <script>
        var tmpServices = services;
        var tmpCategories = [] // Temporary services for the selected project
        var newCategories = [];
        var tmpSelectedProject = [];
        let rowIndex = 0; // To keep track of the row index
        tmpCategories = categories;






        $(document).ready(function() {



            $("#addItem").click(function() {
                $("#addItemModal").modal("show");
            });



            $('#addItemNewForm').on('submit', function(e) {
                e.preventDefault(); // prevent normal form submission

                let form = $(this);
                let url = "{{ route('item-services.services.store') }}"; // Laravel route helper

                // Clear previous errors
                $('#validationErrorsBox').addClass('d-none').empty();
                startLoader();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Handle success (you can close modal, show message, etc.)
                        displaySuccessMessage('Service added successfully!');
                        form[0].reset();
                        $('.categorySelect').last().trigger('change'); // reset select2

                        $("#addItemModal").modal("hide");

                        stopLoader();
                    },
                    error: function(xhr) {
                        stopLoader();
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            displayErrorMessage(xhr.responseJSON.message);
                        } else {
                            displayErrorMessage("Failed to insert");
                        }

                    }
                });
            });

            ///


            addRow();

            function resetDynamicTable() {
                $('#itemRows').empty(); // Clear current rows in the table
                rowIndex = 0; // Reset row index for new rows
                $('#addRow').trigger('click');
            }

            $('#addRow').click(function() {

                addRow();
            });


            function addRow() {
                let options = '';

                // Populate the service dropdown using the JSON data
                tmpServices.forEach(tmpService => {
                    const service = services.find(s => s.id === tmpService.service_id);
                    if (service) {
                        options +=
                            `<option value="${service.id}"  data-item-number="${service.item_number}">${service.title}</option>`;
                    }
                });

                let catoptions = '<option value="" disabled selected>Select a Category</option>'; // Default option
                for (const [id, name] of Object.entries(categories)) {
                    catoptions += `<option value="${id}">${name}</option>`;
                }


                let row = `
            <tr data-index="${rowIndex}">
                <td class="p-1 text-center " style="width: 2%;">${rowIndex + 1}</td>
                <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number" name="itemsArr[${rowIndex}]['item']" value="" ></td>
                <td class="p-1 m-0"  style="width:25%;">
                    <select style="width:100%;" class="form-select categorySelect" name="itemsArr[${rowIndex}]['category_id']" required>
                        ${catoptions}
                    </select>
                </td>
                <td class="p-1 m-0"  style="width:25%;">
                    <select style="width: 100%;"  class="form-select serviceSelect" name="itemsArr[${rowIndex}]['service_id']" required style="min-width:150px;">
                        <option value="" disabled selected>Select a service</option>
                        ${options} <!-- Populate options dynamically -->
                    </select>
                </td>
                <td class="p-1" style="width: 100px;"><input type="number" class="form-control text-right quantity" required value="1" name="itemsArr[${rowIndex}]['quantity']" ></td>
                <td class="p-1" ><input type="number" class="form-control p-1  text-right rates" required value="0.00" name="itemsArr[${rowIndex}]['rate']" style="width: 100px !important;" ></td>

                <td class="p-0 "><input type="number" class="form-control text-right discount " required value="0.00" name="itemsArr[${rowIndex}]['discount']" style="width:90px;background:white;border:none;" readonly></td>
                <td class="p-1 m-0" style="width: 10%;"><input type="number" class="form-control pt-0 text-right taxable" value="0.00" name="itemsArr[${rowIndex}]['taxable']" readonly style="background:white;border:none;"></td>

                <td class="p-0 "><input type="number" class="form-control p-0 text-right tax" value="15.00"  name="itemsArr[${rowIndex}]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                <td class="vat-amount text-end pr-0 pl-5" >0.00</td>

                <td class="p-0 pr-2 "><input type="number" readonly class="total-amount p-0 form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0.00" style="background:white;border:none;width:90px;" ></td>
                <td class="p-1 text-right "><button type="button" class="btn text-danger remove-row"><i class="fa fa-times"></i></button></td>
            </tr>
        `;
                $('#itemRows').append(row);
                rowIndex++;
                $('.serviceSelect,.categorySelect').select2();
            }

            $(document).on('change', '.serviceSelect', function() {

                // var selectedValue = $(this).val(); // Get the selected value

                // var selectedService = services.find(service => service.id == selectedValue);
                // console.log(selectedService.title);

                // var descriptionText = $('<div>').html(selectedService.title || '').text();

                let row = $(this).closest('tr');

                // row.find('textarea[name*="description"]').val(descriptionText ||
                //     '');

                // Set the description in the textarea
                // row.find('.item-number').val('');
                // row.find('.rates').val(selectedService.unit_price || 0);

                calculateRow(row);
                calculateTotals();
            });


            $(document).on('change', '.categorySelect', function() {
                var selectedCategory = $(this).val();
                var row = $(this).closest('tr');

                var serviceSelect = row.find('.serviceSelect');

                $.ajax({
                    url: '{{ route('item-services.services') }}',
                    method: 'GET',
                    data: {
                        item_group_id: selectedCategory
                    },
                    success: function(response) {
                        response = response.data;
                        serviceSelect.empty();
                        // serviceSelect.append(
                        //     '<option value="" disabled selected>Select a service</option>');

                        response.forEach(function(service) {
                            serviceSelect.append(new Option(service.title, service.id));
                        });

                        // Refresh Select2
                        serviceSelect.select2();
                    },
                    error: function() {
                        serviceSelect.empty();
                        serviceSelect.append(
                            '<option value="" disabled selected>Error loading services</option>'
                        );
                    }
                });


                // var serviceSelect = row.find('.serviceSelect');
                // serviceSelect.empty();
                // serviceSelect.append(
                //     '<option value="" disabled selected>Select a service</option>'); // Add default option


                // services.forEach(service => {

                //     if (service.item_group_id == selectedCategory) {
                //         serviceSelect.append(new Option(service.title, service.id));
                //     }


                // });
                // serviceSelect.select2();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input', '.quantity, .rates', function() {
                let row = $(this).closest('tr');
                calculateRow(row);
                calculateTotals();

            });

            // Update overall totals when totalDiscount is manually changed
            // Update overall totals when totalDiscount is manually changed
            $('#totalDiscount').on('input', function() {
                calculateTotals();
            });

            function calculateRow(row) {
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let rate = parseFloat(row.find('.rates').val()) || 0;

                let vatAmount = (quantity * rate) * 0.15;
                let taxableAmount = quantity * rate;
                let totalAmount = taxableAmount;

                row.find('.taxable').val(taxableAmount.toFixed(2));
                row.find('.vat-amount').text(vatAmount.toFixed(2));
                row.find('.total-amount').val(totalAmount.toFixed(2));
            }

            $("#discount_type").on('change', function() {
                calculateTotals();
            });





            function calculateTotals() {
                let subtotal = 0;
                let vatTotal = 0;
                let taxable = 0;
                let discountType = $('#discount_type').val();
                let manualDiscount = parseFloat($('#totalDiscount').val()) || 0;
                const vatRate = 0.15; // Fixed VAT rate (15%)

                // Get deduction values
                let absentDeduction = parseFloat($('#absentDeduction').val()) || 0;
                let allowanceDeduction = parseFloat($('#allowanceDeduction').val()) || 0;
                let damageDeduction = parseFloat($('#damageDeduction').val()) || 0;
                let totalDeductions = absentDeduction + allowanceDeduction + damageDeduction;

                // Calculate subtotal, VAT, and taxable amount for each row
                $('#itemRows tr').each(function() {
                    let row = $(this);
                    let quantity = parseFloat(row.find('.quantity').val()) || 0;
                    let rate = parseFloat(row.find('.rates').val()) || 0;
                    let taxableAmount = quantity * rate;
                    subtotal += taxableAmount;
                    taxable += taxableAmount;
                });

                globaltaxable = taxable;

                // Apply discount to taxable or subtotal based on discount type
                // let discountAmount = discountType === '0' ? (manualDiscount / 100) * taxable : manualDiscount;
                let discountAmount = manualDiscount;
                let discountedSubtotal = taxable - discountAmount - totalDeductions;

                totalNewTaxable = taxable - discountAmount - totalDeductions; //setting global taxable

                // Update DOM elements
                $('#taxable').val(totalNewTaxable.toFixed(2));
                $('#subtotal').val(taxable.toFixed(2));
                $('#totalVAT').val(vatTotal.toFixed(2));

                $('#includingVat').val((discountedSubtotal + vatTotal).toFixed(2));
                $('#afterDiscount').val(discountedSubtotal.toFixed(2));

                updateRowWithDiscountVat(discountAmount, totalDeductions);
            }

            function updateRowWithDiscountVat(discountAmount, totalDeductions) {
                // Calculate selectedPercentage safely
                let selectedPercentage = discountAmount !== 0 ? (discountAmount / globaltaxable) * 100 : 0;

                // Calculate deduction percentage
                let deductionPercentage = totalDeductions !== 0 ? (totalDeductions / globaltaxable) * 100 : 0;

                if (!isFinite(selectedPercentage)) {
                    selectedPercentage = 0; // Handle invalid result
                }
                if (!isFinite(deductionPercentage)) {
                    deductionPercentage = 0; // Handle invalid result
                }

                globalVatAmount = 0;
                $('#itemRows tr').each(function() {
                    let row = $(this);
                    let quantity = parseFloat(row.find('.quantity').val()) || 0;
                    let rate = parseFloat(row.find('.rates').val()) || 0;

                    let rowDisk = (selectedPercentage * rate / 100 * quantity);
                    let rowDeduction = (deductionPercentage * rate / 100 * quantity);

                    let vatAmount = ((quantity * rate) - rowDisk - rowDeduction) * 0.15;
                    let taxableAmount = (quantity * rate) - rowDisk - rowDeduction;
                    let totalAmount = taxableAmount;

                    row.find('.discount').val(rowDisk.toFixed(2));
                    globalVatAmount += vatAmount;
                    row.find('.taxable').val(taxableAmount.toFixed(2));
                    row.find('.vat-amount').text(vatAmount.toFixed(2));
                    row.find('.total-amount').val(totalAmount.toFixed(2));
                });

                // Calculate net total and rounding difference
                let totalNet = globaltaxable + globalVatAmount - discountAmount - totalDeductions;
                var roundedTotal = totalNet;

                if (isRound) {
                    roundedTotal = Math.round(totalNet);
                    let roundDifference = (roundedTotal - totalNet).toFixed(2);
                    $('#adjustment').val(roundDifference);
                }

                $('#totalVAT').val(globalVatAmount.toFixed(2));
                $("#totalAmt").val(roundedTotal);
                $('#netTotal').val(roundedTotal.toFixed(2));
            }
            $(document).ready(function() {
                // ... your existing document ready code ...

                // Add event listeners for deduction fields
                $('.deduction-field').on('input', function() {
                    calculateTotals();
                });
            });

            $('#discount_type').on('change', function() {
                if ($(this).val() == '0') { // Show percentage dropdown if % selected
                    $('#percentage_discount').show();
                    $('#totalDiscount').prop('readonly', true);
                } else { // Hide if $ selected
                    $('#percentage_discount').hide();
                    $('#totalDiscount').prop('readonly', false);
                    $('#totalDiscount').val(0); // Reset discount input
                }
                $('#percentage_discount').trigger('change');
            });

            $('#percentage_discount').on('change', function() {
                let selectedPercentage = $(this).val();
                if (selectedPercentage) {

                    selectedPercentage = parseFloat(selectedPercentage) || 0;
                    let discAmount = globaltaxable * (selectedPercentage / 100);
                    $('#totalDiscount').val(discAmount.toFixed(2));
                    calculateTotals();
                }
            });

        });



        $(document).on('click', '#saveAsDraft, #saveAndSend', function(e) {
            e.preventDefault();

            // Customer validation
            if (!validateCustomer()) {
                return false;
            }

            let paymentStatus = $(this).data('status');

            let myForm = document.getElementById('manualSalesForm');
            let formData = new FormData(myForm);

            formData.append('payment_status', paymentStatus);

            let index = 0;
            let title, desc, qty, rate, amount, totalAmount;
            let itemTaxes = [];
            totalAmount = $('.total-numbers').text();
            $('.items-container>tr').each(function() {
                itemTaxes = [];
                title = $(this).find('.item-name').val();
                desc = $(this).find('.item-description').val();
                qty = $(this).find('.qty').val();
                rate = $(this).find('.rate').val();
                amount = $(this).find('.item-amount').text();
                $.each($($(this).find('.tax-rates option:selected')), function() {
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
            $('#taxesListTable>tr').each(function() {
                taxValue = $(this).find('.tax-value').text();
                taxValue = taxValue.replace('%', '');
                taxAmount = $(this).find('.footer-tax-numbers').text();
                formData.append('taxes[' + taxValue + ']', taxAmount);
            });

            formData.append('total_amount', totalAmount);
            formData.append('sub_total', $('#subTotal').text());

            $.ajax({
                url: route('manual-sales.store'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    startLoader();
                },
                success: function(result) {
                    if (result.success) {
                        let invoiceId = result.data.id;
                        window.location = invoiceUrl;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                    submit = false;
                },
                complete: function() {
                    stopLoader();
                },
            });

        });
    </script>
    {{-- <script>
        var tmpServices = services;
        var tmpCategories = [] // Temporary services for the selected project
        var newCategories = [];
        var tmpSelectedProject = [];
        let rowIndex = 0; // To keep track of the row index
        tmpCategories = categories;






        $(document).ready(function() {



            $("#addItem").click(function() {
                $("#addItemModal").modal("show");
            });



            $('#addItemNewForm').on('submit', function(e) {
                e.preventDefault(); // prevent normal form submission

                let form = $(this);
                let url = "{{ route('item-services.services.store') }}"; // Laravel route helper

                // Clear previous errors
                $('#validationErrorsBox').addClass('d-none').empty();
                startLoader();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Handle success (you can close modal, show message, etc.)
                        displaySuccessMessage('Service added successfully!');
                        form[0].reset();
                        $('.categorySelect').last().trigger('change'); // reset select2

                        $("#addItemModal").modal("hide");

                        stopLoader();
                    },
                    error: function(xhr) {
                        stopLoader();
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            displayErrorMessage(xhr.responseJSON.message);
                        } else {
                            displayErrorMessage("Failed to insert");
                        }

                    }
                });
            });

            ///


            addRow();

            function resetDynamicTable() {
                $('#itemRows').empty(); // Clear current rows in the table
                rowIndex = 0; // Reset row index for new rows
                $('#addRow').trigger('click');
            }

            $('#addRow').click(function() {

                addRow();
            });


            function addRow() {
                let options = '';

                // Populate the service dropdown using the JSON data
                tmpServices.forEach(tmpService => {
                    const service = services.find(s => s.id === tmpService.service_id);
                    if (service) {
                        options +=
                            `<option value="${service.id}"  data-item-number="${service.item_number}">${service.title}</option>`;
                    }
                });

                let catoptions = '<option value="" disabled selected>Select a Category</option>'; // Default option
                for (const [id, name] of Object.entries(categories)) {
                    catoptions += `<option value="${id}">${name}</option>`;
                }


                let row = `
            <tr data-index="${rowIndex}">
                <td class="p-1 text-center " style="width: 2%;">${rowIndex + 1}</td>
                <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number" name="itemsArr[${rowIndex}]['item']" value="" ></td>
                <td class="p-1 m-0"  style="width:25%;">
                    <select style="width:100%;" class="form-select categorySelect" name="itemsArr[${rowIndex}]['category_id']" required>
                        ${catoptions}
                    </select>
                </td>
                <td class="p-1 m-0"  style="width:25%;">
                    <select style="width: 100%;"  class="form-select serviceSelect" name="itemsArr[${rowIndex}]['service_id']" required style="min-width:150px;">
                        <option value="" disabled selected>Select a service</option>
                        ${options} <!-- Populate options dynamically -->
                    </select>
                </td>
                <td class="p-1" style="width: 100px;"><input type="number" class="form-control text-right quantity" required value="1" name="itemsArr[${rowIndex}]['quantity']" ></td>
                <td class="p-1" ><input type="number" class="form-control p-1  text-right rates" required value="0.00" name="itemsArr[${rowIndex}]['rate']" style="width: 100px !important;" ></td>

                <td class="p-0 "><input type="number" class="form-control text-right discount " required value="0.00" name="itemsArr[${rowIndex}]['discount']" style="width:90px;background:white;border:none;" readonly></td>
                <td class="p-1 m-0" style="width: 10%;"><input type="number" class="form-control pt-0 text-right taxable" value="0.00" name="itemsArr[${rowIndex}]['taxable']" readonly style="background:white;border:none;"></td>

                <td class="p-0 "><input type="number" class="form-control p-0 text-right tax" value="15.00"  name="itemsArr[${rowIndex}]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                <td class="vat-amount text-end pr-0 pl-5" >0.00</td>

                <td class="p-0 pr-2 "><input type="number" readonly class="total-amount p-0 form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0.00" style="background:white;border:none;width:90px;" ></td>
                <td class="p-1 text-right "><button type="button" class="btn text-danger remove-row"><i class="fa fa-times"></i></button></td>
            </tr>
        `;
                $('#itemRows').append(row);
                rowIndex++;
                $('.serviceSelect,.categorySelect').select2();
            }

            $(document).on('change', '.serviceSelect', function() {

                // var selectedValue = $(this).val(); // Get the selected value

                // var selectedService = services.find(service => service.id == selectedValue);
                // console.log(selectedService.title);

                // var descriptionText = $('<div>').html(selectedService.title || '').text();

                let row = $(this).closest('tr');

                // row.find('textarea[name*="description"]').val(descriptionText ||
                //     '');

                // Set the description in the textarea
                // row.find('.item-number').val('');
                // row.find('.rates').val(selectedService.unit_price || 0);

                calculateRow(row);
                calculateTotals();
            });


            $(document).on('change', '.categorySelect', function() {
                var selectedCategory = $(this).val();
                var row = $(this).closest('tr');

                var serviceSelect = row.find('.serviceSelect');

                $.ajax({
                    url: '{{ route('item-services.services') }}',
                    method: 'GET',
                    data: {
                        item_group_id: selectedCategory
                    },
                    success: function(response) {
                        response = response.data;
                        serviceSelect.empty();
                        // serviceSelect.append(
                        //     '<option value="" disabled selected>Select a service</option>');

                        response.forEach(function(service) {
                            serviceSelect.append(new Option(service.title, service.id));
                        });

                        // Refresh Select2
                        serviceSelect.select2();
                    },
                    error: function() {
                        serviceSelect.empty();
                        serviceSelect.append(
                            '<option value="" disabled selected>Error loading services</option>'
                        );
                    }
                });


                // var serviceSelect = row.find('.serviceSelect');
                // serviceSelect.empty();
                // serviceSelect.append(
                //     '<option value="" disabled selected>Select a service</option>'); // Add default option


                // services.forEach(service => {

                //     if (service.item_group_id == selectedCategory) {
                //         serviceSelect.append(new Option(service.title, service.id));
                //     }


                // });
                // serviceSelect.select2();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input', '.quantity, .rates', function() {
                let row = $(this).closest('tr');
                calculateRow(row);
                calculateTotals();

            });

            // Update overall totals when totalDiscount is manually changed
            // Update overall totals when totalDiscount is manually changed
            $('#totalDiscount').on('input', function() {
                calculateTotals();
            });

            function calculateRow(row) {
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let rate = parseFloat(row.find('.rates').val()) || 0;

                let vatAmount = (quantity * rate) * 0.15;
                let taxableAmount = quantity * rate;
                let totalAmount = taxableAmount;

                row.find('.taxable').val(taxableAmount.toFixed(2));
                row.find('.vat-amount').text(vatAmount.toFixed(2));
                row.find('.total-amount').val(totalAmount.toFixed(2));
            }

            $("#discount_type").on('change', function() {
                calculateTotals();
            });





            function calculateTotals() {
                let subtotal = 0;
                let vatTotal = 0;
                let taxable = 0;
                let discountType = $('#discount_type').val();
                let manualDiscount = parseFloat($('#totalDiscount').val()) || 0;
                const vatRate = 0.15; // Fixed VAT rate (15%)


                // Calculate subtotal, VAT, and taxable amount for each row
                $('#itemRows tr').each(function() {
                    let row = $(this);
                    let quantity = parseFloat(row.find('.quantity').val()) || 0;
                    let rate = parseFloat(row.find('.rates').val()) || 0;

                    let taxableAmount = quantity * rate;

                    subtotal += taxableAmount;
                    taxable += taxableAmount;


                });
                globaltaxable = taxable;
                // Apply discount to taxable or subtotal based on discount type
                //  let discountAmount = discountType === '0' ? (manualDiscount / 100) * taxable : manualDiscount;
                let discountAmount = manualDiscount;
                let discountedSubtotal = taxable - discountAmount;



                totalNewTaxable = taxable - discountAmount; //setting global taxable


                // Update DOM elements
                $('#taxable').val(totalNewTaxable.toFixed(2));
                $('#subtotal').val(taxable.toFixed(2));
                $('#totalVAT').val(vatTotal.toFixed(2));

                $('#includingVat').val((discountedSubtotal + vatTotal).toFixed(2));
                $('#afterDiscount').val(discountedSubtotal.toFixed(2));

                updateRowWithDiscountVat(discountAmount);

                // calculateRowDiscount(discountType, discountAmount, taxable);
            }

            function updateRowWithDiscountVat(discountAmount) {


                // Calculate selectedPercentage safely


                let selectedPercentage = discountAmount !== 0 ? (discountAmount / globaltaxable) * 100 : 0;

                // Check for Infinity or NaN
                if (!isFinite(selectedPercentage)) {
                    selectedPercentage = 0; // Handle invalid result
                }
                globalVatAmount = 0;
                $('#itemRows tr').each(function() {
                    let row = $(this);
                    let quantity = parseFloat(row.find('.quantity').val()) || 0;
                    let rate = parseFloat(row.find('.rates').val()) || 0;
                    let rowDisk = (selectedPercentage * rate / 100 * quantity);

                    let vatAmount = ((quantity * rate) - rowDisk) * 0.15;
                    let taxableAmount = (quantity * rate) - rowDisk;
                    let totalAmount = taxableAmount;

                    row.find('.discount').val(rowDisk.toFixed(2));

                    globalVatAmount += vatAmount;
                    row.find('.taxable').val(taxableAmount.toFixed(2));
                    row.find('.vat-amount').text(vatAmount.toFixed(2));
                    row.find('.total-amount').val(totalAmount.toFixed(2));


                });

                // Calculate net total and rounding difference
                let totalNet = globaltaxable + globalVatAmount - discountAmount;
                var roundedTotal = totalNet;

                if (isRound) {
                    roundedTotal = Math.round(totalNet);
                    let roundDifference = (roundedTotal - totalNet).toFixed(2);
                    $('#adjustment').val(roundDifference);
                }
                $('#totalVAT').val(globalVatAmount.toFixed(2));

                $("#totalAmt").val(roundedTotal);
                $('#netTotal').val(roundedTotal.toFixed(2));
            }


            $('#discount_type').on('change', function() {
                if ($(this).val() == '0') { // Show percentage dropdown if % selected
                    $('#percentage_discount').show();
                    $('#totalDiscount').prop('readonly', true);
                } else { // Hide if $ selected
                    $('#percentage_discount').hide();
                    $('#totalDiscount').prop('readonly', false);
                    $('#totalDiscount').val(0); // Reset discount input


                }
                $('#percentage_discount').trigger('change');
            });

            $('#percentage_discount').on('change', function() {
                let selectedPercentage = $(this).val();
                if (selectedPercentage) {

                    selectedPercentage = parseFloat(selectedPercentage) || 0;
                    let discAmount = globaltaxable * (selectedPercentage / 100);
                    $('#totalDiscount').val(discAmount.toFixed(2));
                    calculateTotals();
                }
            });

        });



        $(document).on('click', '#saveAsDraft, #saveAndSend', function(e) {
            e.preventDefault();

            let paymentStatus = $(this).data('status');

            let myForm = document.getElementById('manualSalesForm');
            let formData = new FormData(myForm);

            formData.append('payment_status', paymentStatus);

            let index = 0;
            let title, desc, qty, rate, amount, totalAmount;
            let itemTaxes = [];
            totalAmount = $('.total-numbers').text();
            $('.items-container>tr').each(function() {
                itemTaxes = [];
                title = $(this).find('.item-name').val();
                desc = $(this).find('.item-description').val();
                qty = $(this).find('.qty').val();
                rate = $(this).find('.rate').val();
                amount = $(this).find('.item-amount').text();
                $.each($($(this).find('.tax-rates option:selected')), function() {
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
            $('#taxesListTable>tr').each(function() {
                taxValue = $(this).find('.tax-value').text();
                taxValue = taxValue.replace('%', '');
                taxAmount = $(this).find('.footer-tax-numbers').text();
                formData.append('taxes[' + taxValue + ']', taxAmount);
            });

            formData.append('total_amount', totalAmount);
            formData.append('sub_total', $('#subTotal').text());

            $.ajax({
                url: route('manual-sales.store'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    startLoader();
                },
                success: function(result) {
                    if (result.success) {
                        let invoiceId = result.data.id;
                        window.location = invoiceUrl;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                    submit = false;
                },
                complete: function() {
                    stopLoader();
                },
            });

        });
    </script> --}}
@endsection
