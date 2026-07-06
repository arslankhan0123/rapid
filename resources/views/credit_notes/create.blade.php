@extends('layouts.app')
@section('title')
    {{ __('messages.credit_notes') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ mix('assets/css/credit_notes/credit_notes.css') }}">
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
            /* height: 38px !important; */

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

        #inputBranch {
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
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.credit_note.new_credit_note') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                {{ Form::open(['route' => 'credit-notes.store', 'validated' => false, 'id' => 'creditNoteForm']) }}
                @include('credit_notes.address_modal')
                @include('credit_notes.fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('credit_notes.templates.templates')
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
        let taxData = JSON.parse('@json($data['taxes'])');
        let isCreate = true;
        let createData = true;
        let createCreditNoteAddress = true;
        let defaultServices = @json($data['items']);
        let services = @JSON($services);
        const projects = @json($projects);
        let categories = @JSON($categories);
        let customers = @JSON($customers);
        let isRound = "{{ $isRound }}";
        isRound = isRound === "1" || isRound === "true";
    </script>
    <script src="{{ mix('assets/js/sales/sales.js') }}"></script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/credit-notes/credit-notes.js') }}"></script>
    <script>
        $(document).ready(function() {
            let termIndex = 1; // To keep track of SL number

            // Add selected term to the table
            // $('#terms_dropdown').change(function() {
            //     let selectedValue = $(this).val();
            //     let selectedText = $('#terms_dropdown option:selected').text();

            //     if (selectedValue && selectedText) {
            //         // Check if the term is already added
            //         if ($('#terms_table tbody').find(`tr[data-id="${selectedValue}"]`).length === 0) {

            //             $('#terms_table tbody').append(`
        //             <tr data-id="${selectedValue}" class='mt-1'>
        //                 <td>${termIndex}</td>
        //                 <td class='p-0 m-0'> <textarea readonly name="description[]" class='form-control' style='height:120px;width:100%;' >${selectedText}</textarea><input type='hidden' name='terms[]' value="${selectedValue}" </td>
        //                 <td><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
        //             </tr>
        //         `);
            //             termIndex++; // Increment the index for the next term
            //         } else {
            //             alert('This term has already been added.');
            //         }
            //     }
            // });

            // // Remove term row
            // $(document).on('click', '.delete-row', function() {
            //     $(this).closest('tr').remove();
            //     termIndex--; // Decrement index when a row is removed
            //     // Re-number the serial numbers
            //     $('#terms_table tbody tr').each(function(index) {
            //         $(this).find('td:first').text(index + 1);
            //     });
            // });
        });
    </script>
    <script>
        function processingBtn(buttonSelector, state) {
            var $button = $(buttonSelector);
            if (state === 1) {
                // Show loading and disable button
                $button.prop('disabled', true); // Disable the button
                $button.html('processing...'); // Change button text to indicate loading
            } else if (state === 0) {
                // Reset button state
                $button.prop('disabled', false); // Enable the button
                $button.html('Submit'); // Reset button text
            } else if (state === 3) {
                // Reset button state
                $button.prop('disabled', true); // Enable the button
                $button.html('Submit'); // Reset button text
            }
        }
    </script>


    <script>
        $('#addItemSelectBox').on('select2:open', function() {
            $('#addItemSelectBox').val('').change();
        });


        function updateTerms(selectedProjectId) {
            var thisSelectedProject = projects.find(project => project.id == selectedProjectId);
            // Get the terms for the selected project
            const terms = thisSelectedProject ? thisSelectedProject.terms : [];
            // Clear the existing rows in the terms table
            $('#terms_table tbody').empty();


            // Populate the terms table with terms (no delete button)
            terms.forEach((term, index) => {
                $('#terms_table tbody').append(`
            <tr data-id="${term.terms_id}" class='mt-1'>
                <td style="width:50px;">${index + 1}</td>
                <td class='p-0 m-0'>
                    <textarea readonly name="description[]" class='form-control' style='height:120px;width:100%; text-align: left; direction: ltr;background:white'>${strip_tags(term.description)}</textarea>
                    <input type='hidden' name='terms[]' value="${term.terms_id}">
                </td>
            </tr>
        `);
            });
        }

        function strip_tags(str) {
            if ((str === null) || (str === '')) return '';
            else str = str.toString();
            return str.replace(/<\/?[^>]+(>|$)/g, ""); // Remove HTML tags
        }
    </script>
    {{-- new js section --}}
    <script>
        $(document).ready(function() {
            $('.serviceSelect').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('.categorySelect').select2({
                width: '100%', // Set the width of the select element
                // allowClear: true // Allow clearing the selection
            });
            $('#terms_dropdown').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
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


            $('.credit_noteDate').datetimepicker({
                format: 'YYYY-MM-DD',
                useCurrent: false,
                sideBySide: true,

                // maxDate: new Date(),
                icons: {
                    next: 'fa fa-chevron-right',
                    previous: 'fa fa-chevron-left',
                },
            });
        });
    </script>
    <script>
        var tmpServices = []; // Temporary services for the selected project
        let rowIndex = 0; // To keep track of the row index
        var tmpServices = [];
        var tmpCategories = [] // Temporary services for the selected project
        var newCategories = [];
        var tmpSelectedProject = [];

        tmpCategories = categories;

        $(document).ready(function() {

            $('#invoice_id').on('keyup', function() {
                var invoiceId = $(this).val(); // Get the current input value

                // Check if the value is not empty
                if (invoiceId.length > 0) {
                    // Perform the AJAX request
                    $.ajax({
                        url: "{{ route('credit-notes.invoice') }}", // Replace with the actual route for fetching invoice data
                        type: 'GET', // Use GET to fetch data
                        data: {
                            invoice_id: invoiceId
                        },
                        success: function(response) {
                            $(".open").hide();
                            if (response.success && response.data.project !== null) {
                                $(".open").show();
                                $("#projectSelectBox").val(response.data.project ? response.data
                                    .project.project_name : '');

                                if (response.data.project.po_number) {
                                    $("#po_number").val(response.data.project.po_number);
                                }

                                if (response.data.customer_id) {
                                    $("#customerSelectBox").val(response.data.customer_id)
                                        .trigger('change');
                                    $("#customerID").val(response.data.customer_id);

                                    var customer = customers.find(c => c.id == response.data
                                        .customer_id);
                                    $('#vendor_code').val(customer?.vendor_code ?? '');
                                }


                                if (response.data.branch_id) {
                                    $('#inputBranch').val(response.data.branch_id).trigger(
                                        'change');
                                }

                                // const nextNumber = "{{ $nextNumber }}";
                                // if (response.data.branch_id) {
                                //     // Update the creditNoteNumber field with the format "branch_id/nextNumber"
                                //     $('#creditNoteNumber').val(response.data.branch_id + '/' + nextNumber);
                                // } else {
                                //     // Clear the creditNoteNumber field if no branch is selected
                                //     $('#creditNoteNumber').val('');
                                // }

                                $(".currency-select-box").val(response.data.currency).trigger(
                                    'change');
                                $("#currencyInput").val(response.data.currency);

                                updateServiceDropdown(response.data.project.id);

                            } else {
                                $("#projectSelectBox").val('');
                                $("#po_number").val('');
                                $('#itemRows').empty();
                                calculateTotals();
                            }
                        },
                    });
                }
            });


            $('#projectSelect').change(function() {
                const selectedProjectId = $(this).val();
                updateServiceDropdown(selectedProjectId);
            });

            function updateServiceDropdown(projectId) {
                const serviceSelect = $('#serviceSelect'); // Make sure this ID matches your HTML
                serviceSelect.empty(); // Clear the current options
                $('#addRow').prop('disabled', true); // Disable add row button initially

                // Find the selected project
                const selectedProject = projects.find(project => project.id == projectId);
                if (selectedProject) {


                    updateTerms(projectId);

                    tmpSelectedProject = selectedProject;
                    tmpServices = selectedProject.services; // Get the services for the selected project
                    // Filter services based on the selected project
                    const filteredServices = services.filter(service =>
                        tmpServices.some(tmpService => tmpService.service_id === service.id)
                    );

                    filteredServices.forEach(service => {
                        serviceSelect.append(new Option(`${service.title} - Price: ${service.rate}`, service
                            .id));
                    });

                    newCategories = [];
                    // Step 1: Extract unique category_ids from selectedProject.services
                    const categoryIds = [...new Set(selectedProject.services.map(service => service.category_id))];
                    // Step 2: Create an object for the selected categories
                    const tmpCategory = {};

                    // Step 3: Populate tmpCategory with the relevant categories
                    categoryIds.forEach(id => {


                        if (categories[id]) { // Check if the category exists
                            tmpCategory[id] = categories[id];
                        }

                    });
                    newCategories = tmpCategory;

                    $('#addRow').prop('disabled', filteredServices.length === 0);

                    // Reset the dynamic table when the project changes
                    resetDynamicTable();
                }
            }

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
                            `<option value="${service.id}" data-item-number="${service.item_number}">${service.title}</option>`;
                    }
                });

                let catoptions = '<option value="" disabled selected>Select a Category</option>'; // Default option
                for (const [id, name] of Object.entries(newCategories)) {
                    catoptions += `<option value="${id}">${name}</option>`;
                }

                let row = `
                <tr data-index="${rowIndex}">
                    <td>${rowIndex + 1}</td>
                    <td class="p-1" style="width: 13%;"><input type="text" class="form-control item-number" name="itemsArr[${rowIndex}]['item']" value="" readonly></td>
                    <td class="p-1 m-0"  style="width:25%;">
                        <select style="width:100%;" class="form-select categorySelect" name="itemsArr[${rowIndex}]['category_id']" required>
                            ${catoptions}
                        </select>
                    </td>
                    <td class="p-1 m-0" style="width:25%;">
                        <select style="width: 100%;"  class="form-select serviceSelect" name="itemsArr[${rowIndex}]['service_id']" required style="min-width:150px;">
                            <option value="" disabled selected>Select a service</option>
                            ${options} <!-- Populate options dynamically -->
                        </select>
                    </td>
                     <td class="p-1" style="width: 100px;"><input type="number" class="form-control text-right quantity" required value="1" name="itemsArr[${rowIndex}]['quantity']" ></td>
                    <td class="p-1" ><input type="number" class="form-control p-1  text-right rates" required value="0.00" name="itemsArr[${rowIndex}]['rate']" style="width: 100px !important;" readonly></td>

                    <td class="p-0"><input type="number" class="form-control text-right discount p-0" required value="0.00" name="itemsArr[${rowIndex}]['discount']" style="width:90px;background:white;border:none;" readonly></td>
                    <td class="p-1 m-0" style="width: 10%;"><input type="number" class="form-control p-0 text-right taxable" value="0.00" name="itemsArr[${rowIndex}]['taxable']" readonly style="background:white;border:none;"></td>

                    <td class="p-0"><input type="number" class="form-control p-0 text-right tax" value="15.00"  name="itemsArr[${rowIndex}]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                    <td class="vat-amount text-end pr-0 pl-5" >0.00</td>

                    <td class="p-0 pr-2"><input type="number" readonly class="total-amount p-0 form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0.00" style="background:white;border:none;width:90px;" ></td>
                    <td class="p-1 text-right"><button type="button" class="btn text-danger remove-row"><i class="fa fa-times"></i></button></td>
                </tr>
            `;
                $('#itemRows').append(row);
                rowIndex++;
                $('.serviceSelect,.categorySelect').select2();
            }

            $(document).on('change', '.serviceSelect', function() {


                var selectedValue = $(this).val(); // Get the selected value
                var selectedService = tmpSelectedProject.services.find(service => service.service_id ==
                    selectedValue);

                var itemNumber = selectedService.ref_no;
                var descriptionText = $('<div>').html(selectedService.description || '').text();


                let row = $(this).closest('tr');



                row.find('.item-number').val(itemNumber || '');
                row.find('.rates').val(selectedService.unit_price || 0);

                calculateRow(row);
                calculateTotals();
            });

            $(document).on('change', '.categorySelect', function() {
                var selectedCategory = $(this).val();

                // Find the closest row containing the changed categorySelect
                var row = $(this).closest('tr'); // This gets the closest <tr> parent

                // Find the serviceSelect within the same row and clear it
                var serviceSelect = row.find('.serviceSelect');
                serviceSelect.empty();
                serviceSelect.append(
                    '<option value="" disabled selected>Select a service</option>'); // Add default option

                // Iterate through services and append matching options
                // Get matching services

                tmpSelectedProjectServices = tmpSelectedProject.services;
                const matchingServices = services.filter(service =>
                    tmpSelectedProjectServices.some(tmpService => tmpService.service_id === service.id)
                );

                // Output the matching services
                //   console.log(matchingServices);


                matchingServices.forEach(service => {

                    if (service.item_group_id == selectedCategory) {

                        serviceSelect.append(new Option(service.title, service.id));
                    }


                });

                // Refresh the Select2 to reflect the updated options
                serviceSelect.select2();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input', '.quantity, .rates, .discount', function() {
                let row = $(this).closest('tr');
                calculateRow(row);
                calculateTotals();
            });

            // Update overall totals when totalDiscount is manually changed
            $('#totalDiscount').on('input', function() {
                calculateTotals();
            });

            function calculateRow(row) {
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let rate = parseFloat(row.find('.rates').val()) || 0;

                let vatAmount = (quantity * rate) * 0.15;
                let taxableAmount = quantity * rate;
                let totalAmount = taxableAmount + vatAmount;

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
                // console.log("Taxable", taxable,discountAmount);


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
                    let totalAmount = taxableAmount + vatAmount;

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
    </script>
@endsection
