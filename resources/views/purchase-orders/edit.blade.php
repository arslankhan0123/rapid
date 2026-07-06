@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-orders.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ mix('assets/css/estimates/estimates.css') }}">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
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

        .serviceSelect-custom-dropdown {
            width: 550px !important;
            /* Set dropdown width to 300px */
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
            height: 38px !important;
        }

        .smallInput {
            /* height: 30px !important; */
        }

        .fa-times {
            color: black;
        }

        .fa-times:hover {
            color: red;

        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.purchase-orders.edit') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                {{ Form::open(['route' => ['purchase-orders.update', $estimate->id], 'validated' => false, 'method' => 'POST', 'id' => 'editOrderForm']) }}
                @include('purchase-orders.edit_fields_new')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('purchase-orders.templates.templates')
    @include('tags.common_tag_modal')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let editData = true;
        let estimateEdit = true;
        let taxData = JSON.parse('@json($data['taxes'])');
        let productUrl = "{{ route('products.index') }}";
        let estimateEditURL = "{{ route('estimates.index') }}";
        let editEstimateAddress = true;
        let customerURL = "{{ route('get.customer.address') }}";
        let services = @JSON($services);
        let categories = @JSON($categories);
        var $units = @json($units);

        $('.datepicker').datetimepicker({
            format: 'DD-MM-YYYY', // Date format (adjust as needed)
            useCurrent: false, // Prevents using the current date by default
            showClose: true, // Show a "close" button
            showClear: true, // Show a "clear" button
            showTodayButton: true // Show a "today" button
        });
    </script>
    <script src="{{ mix('assets/js/sales/sales.js') }}"></script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {

            const suppliers = @json($data['customers']);
            const supplierList = Object.entries(suppliers).map(([id, name]) => ({
                id: parseInt(name.id),
                name: name.company_name,
                contact_person: name.contact_person ?? '',
                country: name.supplier_country ? name.supplier_country.name ?? '' : '',
                state: name.supplier_state ? name.supplier_state.name ?? '' : '',
                contact_person: name.contact_person ?? '',
                po_box: name.po_box ?? '',
                mailing_address: name.mailing_address ?? '',
                city: name.city ?? '',
                whatsapp: name.whatsapp ?? '',
                email: name.email ?? '',
                phone: name.phone ?? '',
                groups: name.groups ?
                    name.groups
                    .map(group => group.group?.name) // Extract each group's name
                    .filter(name => name) // Filter out any undefined or null names
                    .join(', ') // Concatenate names with a comma and space
                    :
                    '' // Default to an empty string if no groups exist
            }));
            $("#supplierAutocomplete").autocomplete({
                source: supplierList.map(supplier => ({
                    label: supplier.name,
                    value: supplier.id,
                    details: supplier // Include all supplier details for later use
                })),
                select: function(event, ui) {
                    const selectedSupplier = ui.item.details;
                    $(this).val(ui.item.label); // Set the selected name in the input
                    $("#supplierNumber").val(ui.item.value); // Set the selected ID in hidden field
                    // Populate all corresponding fields
                    $(".contact_person").val(selectedSupplier.contact_person);
                    $(".country").val(selectedSupplier.country);
                    $(".state").val(selectedSupplier.state);
                    $(".po_box").val(selectedSupplier.po_box);
                    $(".mailing_address").val(selectedSupplier.mailing_address);
                    $(".city").val(selectedSupplier.city);
                    $(".whatsapp").val(selectedSupplier.whatsapp);
                    $(".email").val(selectedSupplier.email);
                    $(".phone").val(selectedSupplier.phone);
                    $(".supplierStatus").val("Active");
                    $(".supplierGroups").val(selectedSupplier.groups);
                    return false;
                }
            });


            // Clear supplierNumber when the input value is modified after selection
            $("#supplierAutocomplete").on("input", function() {
                $("#supplierNumber").val(''); // Clear the supplierNumber hidden field
            });
            // Initialize Select2
            $('#terms_dropdown').select2({
                placeholder: "Select Terms & Conditions",
                dropdownParent: $('#terms_table'),
                width: 'resolve' // Dynamically resolves the width based on the parent element
            });

            // Handle toggle functionality
            $('#toggle_icon').on('click', function() {
                const icon = $(this).find('i');

                // If the select is currently visible, hide it
                if (!$('#terms_dropdown').hasClass('d-none')) {
                    $('#terms_dropdown').addClass('d-none').select2(
                        'destroy'); // Destroy Select2 for hiding
                    $('#description_text').removeClass('d-none'); // Show description
                    icon.removeClass('fa-minus').addClass('fa-plus');
                } else {

                    // Otherwise, show the select and reinitialize Select2
                    $('#terms_dropdown').removeClass('d-none').select2({
                        placeholder: "Select Terms & Conditions",
                        dropdownParent: $('#terms_table'),
                        width: 'resolve'
                    });
                    $('#description_text').addClass('d-none'); // Hide description
                    icon.removeClass('fa-plus').addClass('fa-minus');
                }
            });

            // $('#toggle_icon').trigger('click');
            // 1000 milliseconds = 1 second

            let termIndex = 1; // To keep track of SL number
            // Add selected term to the table
            $('#terms_dropdown').change(function() {
                let selectedValue = $(this).val();
                let selectedText = $('#terms_dropdown option:selected').text();
                let fullText = $('#terms_dropdown option:selected').data('full-text');
                if (selectedValue && selectedText) {
                    // Check if the term is already added
                    if ($('#terms_table tbody').find(`tr[data-id="${selectedValue}"]`).length === 0) {

                        $('#terms_table tbody').append(`
                        <tr data-id="${selectedValue}" class='mt-1'>
                            <td>${termIndex}</td>
                            <td class='p-0 m-0'> <textarea name="terms_description[]" class='form-control' style='width:100%;height:120px !important;'>${fullText}</textarea><input type='hidden' name='terms[]' value="${selectedValue}" </td>
                            <td><button type="button" class="btn btn-sm delete-row"><i class="fa fa-times"></i></button></td>
                        </tr>
                    `);
                        termIndex++; // Increment the index for the next term
                    } else {
                        alert('This term has already been added.');
                    }
                }
            });

            // Remove term row
            $(document).on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                termIndex--; // Decrement index when a row is removed
                // Re-number the serial numbers
                $('#terms_table tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });



            // Adjust heights on page load
            adjustHeights();

            // Adjust heights on window resize
            $(window).resize(function() {
                adjustHeights();
            });


        });


        function adjustHeights() {
            // Get the window height
            const windowHeight = $(window).height();

            // Calculate heights based on percentages
            const headerHeight = (18.5 / 100) * windowHeight;
            const contentHeight = (32 / 100) * windowHeight;
            const footerHeight = (15 / 100) * windowHeight;


            $('.page_contents').css({
                'height': `${contentHeight}px`,
                overflow: 'auto', // Add scrollbar if overflow
            });

        }
    </script>
    <script>
        $(document).ready(function() {

            $(".btnDocumentNumber").on("click", function() {
                $("#documentModal").modal("show");
            });
            $("#btnTermsNconditons").on("click", function() {
                $("#termsModal").modal("show");
            });

            $(".btnAddress").on("click", function() {
                $("#addressModal").modal("show");
            });
            $('#addItemBtn').on('click', function() {
                $('#createNewItem').modal('show');
            });
            // When .itemName gets focused, move focus to .taxable input
            $('.discountAmount').on('focus', function(e) {
                e.preventDefault(); // Prevent default behavior
                // Move focus to the .taxable input
                $('.rates').focus();
            });
            // When .itemName gets focused, move focus to .taxable input
            $('.taxable').on('focus', function(e) {
                e.preventDefault(); // Prevent default behavior
                // Move focus to the .taxable input
                $('.serviceSelect').focus();
            });

            $(document).on("keydown", "#globalRate", function(event) {
                if (event.key === "Enter" || event.keyCode === 13) {
                    event.preventDefault(); // Prevent default behavior if needed
                    $("#serviceSearch").focus(); // Move focus to the input with id="serviceSearch"
                }
            });
        });
    </script>
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

            $('.serviceSelect').select2({

                width: '200px' // Set the select box width to 200px
            }).on('select2:open', function() {
                // Apply a unique class to the dropdown when it opens
                $('.select2-dropdown').addClass('serviceSelect-custom-dropdown');
            });


            $('.categorySelect').select2({
                width: '100%', // Set the width of the select element
                // allowClear: true // Allow clearing the selection
            });


            // Event listener for when a customer is selected from the dropdown
            $('#supplierSelectBox').on('change', function() {
                // Get the selected customer ID
                var customerId = $(this).val();

                // Get the customer name associated with the selected customer ID
                var customerName = $('#supplierSelectBox option:selected').text();

                // Set the customer name input to the selected customer's name
                $('#customerNameInput').val(customerName);
            });

        });
    </script>





    <script>
        $(document).ready(function() {




            // Mock service data (Replace with your dynamic data or AJAX endpoint)
            const services = @json($services); // Inject Laravel services data

            // Initialize autocomplete
            $("#serviceSearch").autocomplete({
                source: function(request, response) {

                    const filtered = services.filter(service =>
                        service.name.toLowerCase().includes(request.term.toLowerCase()) ||
                        service.barcode?.toLowerCase().includes(request.term.toLowerCase()) ||
                        service.code?.toLowerCase().includes(request.term.toLowerCase())
                    );
                    response(
                        filtered.map(service => ({
                            label: `${service.name} | ${service.barcode || ''} | ${service.code || ''}`,
                            value: service.name,
                            id: service.id,
                            code: service.code,
                            name: service.name,
                            unit: service.unit?.title ?? ''
                        }))
                    );
                },
                minLength: 2, // Trigger after 2 characters
                select: function(event, ui) {
                    addRow(ui.item); // Add the selected service as a new row
                    $(this).val(''); // Clear the input
                    return false;
                }
            });

            let rowIndex = $('#itemRows tr').length; // Initialize rowIndex based on the number of existing rows

            $("#itemRows tr").each(function() {
                const row = $(this);
                calculateRow(row); // Call calculateRow for each row
            });

            calculateTotals(); // Recalculate totals after adding a new row

            function addRow(service) {
                var globalQTY = $('#globalQTY').val(); // Get value from the input with ID 'globalQTY'
                var globalRate = $('#globalRate').val(); // Get value from the input with ID 'globalRate'
                var globalUnit = $('#globalUnit').val();
                const row = `
                    <tr data-index="${rowIndex}">
                        <td style="width: 2%;"  >${rowIndex + 1}</td>
                        <td>
                            ${service.code || ''}
                            <input type="hidden" name="itemsArr[${rowIndex}]['service_id']" value='${service.id}' >
                            <input type="hidden" name="itemsArr[${rowIndex}]['description']" value='${service.name}' >
                        </td>
                        <td style="width: 20%;">${service.name}</td>
                        <td class="p-1"><input  type="number" class="form-control smallInput text-right quantity" required value="${globalQTY}" name="itemsArr[${rowIndex}]['quantity']"></td>
                         <td class="p-1 text-center" >
                            <select name="itemsArr[${rowIndex}]['unit_id']" class="form-control smallInput" style="width: 100px;">
                                ${Object.entries($units).map(([key, value]) => `
                                                <option value="${key}" ${key == globalUnit ? 'selected' : ''}>${value}</option>
                                            `).join('')}
                            </select>
                        </td>
                        <td class="p-1"><input type="number" class="form-control smallInput text-right rates" required value="${globalRate}"  name="itemsArr[${rowIndex}]['rate']"></td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right discount" required value="0" name="itemsArr[${rowIndex}]['discount']">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right discountAmount" name="itemsArr[${rowIndex}]['discountAmount']" readonly>
                        </td>


                        <td class="p-1"><input type="number" class="form-control smallInput text-right taxable" name="itemsArr[${rowIndex}]['taxable']" readonly></td>
                        <td class="p-1"><input type="number" class="form-control smallInput text-right tax" value="15" name="itemsArr[${rowIndex}]['tax']" readonly></td>
                        <td class="vat-amount text-center">0.00</td>
                        <td class="p-1"><input type="number" readonly class="total-amount smallInput form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0"></td>
                        <td class="p-1 text-right"><button type="button" class="btn text-danger remove-row"><i class="fa fa-times"></i></button></td>
                    </tr>
                `;

                $(document).on('focus', '.taxable', function() {
                    // Find the closest row and move to the next row's quantity field
                    const currentRow = $(this).closest('tr');
                    const nextRow = currentRow.next();

                    // Focus the quantity input of the next row if it exists
                    if (nextRow.length) {
                        nextRow.find('.quantity').focus();
                    }
                });
                // Prepend the new row to display it at the top
                $("#itemRows").prepend(row);
                rowIndex++;

                $("#itemRows tr").each(function() {
                    const row = $(this);
                    calculateRow(row); // Call calculateRow for each row
                });

                calculateTotals(); // Recalculate totals after adding a new row
            }

            // Remove row functionality
            $(document).on("click", ".remove-row", function() {
                $(this).closest("tr").remove();
                calculateTotals();
            });

            // Update calculations on input change
            $(document).on("input", ".quantity, .rates, .discount", function() {
                const row = $(this).closest("tr");
                calculateRow(row);
                calculateTotals();
            });

            // Discount logic
            $(document).on("input", "#totalDiscount, #discount_type", function() {
                calculateTotals();
            });

            function calculateRow(row) {
                const quantity = parseFloat(row.find(".quantity").val()) || 0;
                const rate = parseFloat(row.find(".rates").val()) || 0;
                const discountPercent = parseFloat(row.find(".discount").val()) || 0;
                const tax = parseFloat(row.find(".tax").val()) || 0;

                const taxableBeforeDiscount = quantity * rate;
                const discountAmount = (taxableBeforeDiscount * discountPercent) / 100;
                const taxable = taxableBeforeDiscount - discountAmount;
                const vatAmount = taxable * (tax / 100);
                const total = taxable + vatAmount;

                // Update the row fields
                row.find(".discountAmount").val(discountAmount.toFixed(2));
                row.find(".taxable").val(taxable.toFixed(2));
                row.find(".vat-amount").text(vatAmount.toFixed(2));
                row.find(".total-amount").val(total.toFixed(2));
            }

            function calculateTotals() {
                let subtotal = 0;
                let vatTotal = 0;

                // Calculate row totals
                $("#itemRows tr").each(function() {
                    const row = $(this);
                    const taxable = parseFloat(row.find(".taxable").val()) || 0;
                    const vatAmount = parseFloat(row.find(".vat-amount").text()) || 0;

                    subtotal += taxable;
                    vatTotal += vatAmount;
                });

                // Handle discount
                const discountType = $("#discount_type").val(); // 1 for $, 0 for %
                const discountValue = parseFloat($("#totalDiscount").val()) || 0;
                let discountAmount = 0;

                if (discountType === "0") {
                    // Percentage discount
                    discountAmount = subtotal * (discountValue / 100);
                } else {
                    // Flat amount discount
                    discountAmount = discountValue;
                }

                const afterDiscount = subtotal - discountAmount;
                const grandTotal = afterDiscount + vatTotal;

                // Update totals table
                $("#includingVat").text(subtotal.toFixed(2) + " SAR");
                $("#afterDiscount").text(afterDiscount.toFixed(2) + " SAR");
                $("#totalVAT").text(vatTotal.toFixed(2) + " SAR");
                $("#netTotal").text(grandTotal.toFixed(2) + " SAR");

                // Update hidden inputs
                $("#sub_total").val(subtotal.toFixed(2));
                $("#total_amount").val(grandTotal.toFixed(2));

                if (grandTotal > 0) {
                    $('#btnSave').prop('disabled', false); // Enable the button
                } else {
                    $('#btnSave').prop('disabled', true); // Disable the button
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#btnSave').on('click', function() {
                // Clear any previous messages
                $('#responseMessage').html('');

                // Disable the button to prevent multiple clicks
                $(this).prop('disabled', true);

                // Serialize form data
                const formData = $('#editOrderForm').serialize();
                startLoader();
                // Perform the AJAX call
                $.ajax({
                    url: '{{ route('purchase-orders.update', ['order' => $estimate->id]) }}', // Pass $order->id to the route

                    type: 'POST',
                    data: formData,
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        displaySuccessMessage(response.message);
                        const url = route('purchase-orders.index');
                        window.location.href = url;
                        stopLoader();
                    },
                    error: function(xhr) {
                        stopLoader();
                        displayErrorMessage(xhr.responseJSON.message);
                    },
                    complete: function() {
                        stopLoader();
                        $('#btnSave').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
