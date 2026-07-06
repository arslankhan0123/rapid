@extends('layouts.app')
@section('title')
    {{ __('messages.invoice.invoice') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
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

        ,
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoice.edit_invoice') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                {{ Form::open(['route' => ['invoices.update', $invoice->id], 'validated' => false, 'method' => 'POST', 'id' => 'editInvoiceForm']) }}
                @include('invoices.address_modal')
                @include('invoices.edit_fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('invoices.templates.templates')
    @include('tags.common_tag_modal')
    @include('payment_modes.common_payment_mode')
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
        let invoiceEdit = true;
        let taxData = JSON.parse('@json($data['taxes'])');
        let invoiceEditURL = "{{ route('invoices.index') }}";
        let editInvoiceAddress = true;
        let customerURL = "{{ route('get.customer.address') }}";
        let categories = @JSON($categories);
        var allProjects = @json($projects);
        let customers = @JSON($customers);
        let allBranches = @json($usersBranches);

        let isRound = "{{ $isRound }}";
        isRound = isRound === "1" || isRound === "true";
    </script>
    <script src="{{ mix('assets/js/sales/sales.js') }}"></script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ asset('assets/js/invoices/invoices.js') }}"></script>

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

            // Event listener for when a customer is selected from the dropdown
            $('#customerSelectBox').on('change', function() {

                // Get the selected customer ID
                var customerId = $(this).val();
                var customer = customers.find(c => c.id == customerId);
                $('#vendor_code').val(customer?.vendor_code ?? '');


                updateProjects(customerId);

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
            // Cancel button

        });

        function updateProjects(customerId) {
            console.log("Project Updated");
            // Clear the existing options in the project select box
            $('#projectSelect').empty();
            $('#projectSelect').append('<option value="">' + "{{ __('messages.placeholder.select_project') }}" +
                '</option>');


            allProjects.forEach(function(project) {
                var selected = (project.id == invoice.project_id) ? 'selected' : '';
                if (project.customer_id == customerId) {
                    // Add the matching projects to the select box

                    $('#projectSelect').append('<option value="' + project.id + '" ' + selected + '>' + project
                        .project_name +
                        '</option>');


                    //   $("#projectSelect").trigger('change');
                    //creats issue when calling due to edit page
                }
            });

        }
    </script>
    <script>
        $(document).ready(function() {
            let termIndex = $('#terms_table tbody tr').length + 1; // Start index based on existing rows

            // // Add selected term to the table
            // $('#terms_dropdown').change(function() {
            //     let selectedValue = $(this).val();
            //     let selectedText = $('#terms_dropdown option:selected').text();

            //     if (selectedValue && selectedText) {
            //         // Check if the term is already added
            //         if ($('#terms_table tbody').find(`tr[data-id="${selectedValue}"]`).length === 0) {
            //             $('#terms_table tbody').append(`
        //             <tr data-id="${selectedValue}">
        //                 <td>${termIndex}</td>
        //                 <td class='p-0 m-0'>
        //                     <textarea readonly name="description[]" class='form-control' style='height:120px;width:100%;'>${selectedText}</textarea>
        //                     <input type='hidden' name='terms[]' value="${selectedValue}">
        //                 </td>
        //                 <td>
        //                     <button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button>
        //                 </td>
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
        var tmpServices = []; // Temporary services for the selected project
        var newCategories = [];
        var tmpSelectedProject = [];

        let services = @json($services);
        const projects = @json($projects);
        let invoice = @json($invoice);


        const selectedProject = projects.find(project => project.id == invoice.project_id);
        if (selectedProject) {
            tmpSelectedProject = selectedProject;
            tmpServices = selectedProject.services; // Get the services for the selected project
            // Filter services based on the selected project
            const filteredServices = services.filter(service =>
                tmpServices.some(tmpService => tmpService.service_id === service.id)
            );

            newCategories = [];
            const categoryIds = [...new Set(selectedProject.services.map(service => service.category_id))];
            const tmpCategory = {};
            categoryIds.forEach(id => {
                if (categories[id]) { // Check if the category exists
                    tmpCategory[id] = categories[id];
                }

            });
            newCategories = tmpCategory;
        }
    </script>

    <script>
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
                    <textarea readonly name="description[]" class='form-control' style='height:120px;width:100%; text-align: left; direction: ltr; background-color:white;'>${strip_tags(term.description)}</textarea>
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

    {{-- <script>
        let rowIndex = 0; // To keep track of the row index

        function updateBranches(projectId) {
            // Clear the existing options in the branch select box
            $('#branchSelect').empty();

            const selectedProject = allProjects.find(project => project.id == projectId);
            if (selectedProject && selectedProject.branch_id) {
                // Add the branch corresponding to the project's branch_id
                const branchId = selectedProject.branch_id;
                if (allBranches[branchId]) {
                    $('#branchSelect').append('<option value="' + branchId + '">' + allBranches[branchId] +
                        '</option>');
                }
            }
        }

        $(document).ready(function() {
            let rowIndex = $('#itemRows tr').length; // Set the row index based on the existing number of rows
            calculateTotals();
            $('.serviceSelect').select2(); // Initialize select2 for existing elements

            // Calculate for each row on page load (edit page scenario)
            $('#itemRows tr').each(function() {
                calculateRow($(this)); // Calculate each row
            });
            calculateTotals(); // Update overall totals

            $('#projectSelect').change(function() {
                const selectedProjectId = $(this).val();
                updateServiceDropdown(selectedProjectId);
                updateBranches(selectedProjectId);
            });

            function updateServiceDropdown(projectId) {
                const serviceSelect = $('#serviceSelect'); // Make sure this ID matches your HTML
                serviceSelect.empty(); // Clear the current options
                $('#addRow').prop('disabled', true); // Disable add row button initially

                // Find the selected project
                const selectedProject = projects.find(project => project.id == projectId);

                if (selectedProject) {
                    updateTerms(selectedProject.id);
                    if (selectedProject.po_number) {
                        $("#po_number").val(selectedProject.po_number);
                    } else {
                        $("#po_number").val('');
                    }

                    tmpSelectedProject = selectedProject;
                    tmpServices = selectedProject.services; // Get the services for the selected project

                    // Category updates starts here
                    newCategories = [];
                    const categoryIds = [...new Set(selectedProject.services.map(service => service.category_id))];
                    const tmpCategory = {};
                    categoryIds.forEach(id => {
                        if (categories[id]) { // Check if the category exists
                            tmpCategory[id] = categories[id];
                        }
                    });
                    newCategories = tmpCategory;
                    //ends here

                    // Enable the add row button if there are categories available
                    $('#addRow').prop('disabled', Object.keys(newCategories).length === 0);

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
                let catoptions = '<option value="" disabled selected>Select a Category</option>'; // Default option
                for (const [id, name] of Object.entries(newCategories)) {
                    catoptions += `<option value="${id}">${name}</option>`;
                }

                let row = `
                <tr data-index="${rowIndex}">
                    <td class="p-1 text-center " style="width: 2%;">${rowIndex + 1}</td>
                    <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number" name="itemsArr[${rowIndex}]['item']" value="" readonly></td>
                    <td class="p-1 m-0"  style="width:25%;">
                        <select style="width:100%;" class="form-select categorySelect" name="itemsArr[${rowIndex}]['category_id']" required>
                            ${catoptions}
                        </select>
                    </td>
                    <td class="p-1 m-0"  style="width:25%;">
                        <select style="width: 100%;"  class="form-select serviceSelect" name="itemsArr[${rowIndex}]['service_id']" required style="min-width:150px;">
                            <option value="" disabled selected>Select a service</option>
                            <!-- Options will be populated based on category selection -->
                        </select>
                    </td>
                    <td class="p-1" style="width: 100px;"><input type="number" class="form-control text-right quantity" required value="1" name="itemsArr[${rowIndex}]['quantity']" ></td>
                    <td class="p-1" ><input type="number" class="form-control p-1  text-right rates" required value="0.00" name="itemsArr[${rowIndex}]['rate']" style="width: 100px !important;" readonly></td>
                    <td class="p-0"><input type="number" class="form-control text-right discount " required value="0.00" name="itemsArr[${rowIndex}]['discount']" style="width:90px;background:white;border:none;" readonly></td>
                    <td class="p-1 m-0" style="width: 10%;"><input type="number" class="form-control text-right taxable" value="0.00" name="itemsArr[${rowIndex}]['taxable']" readonly style="background:white;border:none;"></td>

                    <td class="p-0"><input type="number" class="form-control  text-right tax" value="15.00"  name="itemsArr[${rowIndex}]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                    <td class="vat-amount text-end pl-5" >0.00</td>

                    <td class="p-0 pr-2"><input type="number" readonly class="total-amount  form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0.00" style="background:white;border:none;width:90px;" ></td>
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

                if (selectedService) {
                    var itemNumber = selectedService.ref_no;
                    var descriptionText = $('<div>').html(selectedService.description || '').text();
                    let row = $(this).closest('tr');
                    row.find('textarea[name*="description"]').val(descriptionText ||
                        ''); // Set the description in the textarea
                    row.find('.item-number').val(itemNumber || '');
                    row.find('.rates').val(selectedService.unit_price || 0);

                    calculateRow(row);
                    calculateTotals();
                }
            });

            $(document).on('change', '.categorySelect', function() {
                var selectedCategory = $(this).val();
                var row = $(this).closest('tr');
                var serviceSelect = row.find('.serviceSelect');

                // Clear service dropdown
                serviceSelect.empty();
                serviceSelect.append('<option value="" disabled selected>Select a service</option>');

                if (selectedCategory && tmpSelectedProject && tmpSelectedProject.services) {
                    // Filter services based on selected category and project services
                    const categoryServices = tmpSelectedProject.services.filter(service =>
                        parseInt(service.category_id) === parseInt(selectedCategory)
                    );

                    // Populate service dropdown with filtered services
                    categoryServices.forEach(function(projectService) {
                        // Find the service details from global services array
                        const serviceDetails = services.find(s => s.id === projectService
                            .service_id);
                        if (serviceDetails) {
                            serviceSelect.append(new Option(serviceDetails.title, serviceDetails
                                .id));
                        }
                    });

                    // Refresh the Select2 to reflect the updated options
                    serviceSelect.select2();
                }
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

                // Check for Infinity or NaN
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

            // Add event listeners for deduction fields
            $(document).ready(function() {
                // Add event listeners for deduction fields
                $('.deduction-field').on('input', function() {
                    calculateTotals();
                });

                // Initialize calculations on page load for edit page
                calculateTotals();
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
    </script> --}}


    <script>
        let rowIndex = 0; // To keep track of the row index

        function updateBranches(projectId) {
            // Clear the existing options in the branch select box
            $('#branchSelect').empty();

            const selectedProject = allProjects.find(project => project.id == projectId);
            if (selectedProject && selectedProject.branch_id) {
                // Add the branch corresponding to the project's branch_id
                const branchId = selectedProject.branch_id;
                if (allBranches[branchId]) {
                    $('#branchSelect').append('<option value="' + branchId + '">' + allBranches[branchId] +
                        '</option>');
                }
            }
        }

        // Function to filter categories for a specific project
        function filterCategoriesForProject(project) {
            const filteredCategories = {};

            if (project && project.services) {
                // Get unique category_ids from project services
                const categoryIds = [...new Set(project.services.map(service => service.category_id))];

                // Create filtered categories object
                categoryIds.forEach(id => {
                    if (categories[id]) { // Check if the category exists in global categories
                        filteredCategories[id] = categories[id];
                    }
                });
            }

            return filteredCategories;
        }

        // Function to update existing rows with filtered categories AND services
        function updateExistingRowsCategoriesAndServices(project) {
            if (!project) return;

            const filteredCategories = filterCategoriesForProject(project);

            // Update all existing rows
            $('#itemRows tr').each(function() {
                const row = $(this);
                const categorySelect = row.find('.categorySelect');
                const serviceSelect = row.find('.serviceSelect');
                const currentCategory = categorySelect.val();
                const currentService = serviceSelect.val();

                // Destroy Select2 first
                if (categorySelect.hasClass('select2-hidden-accessible')) {
                    categorySelect.select2('destroy');
                }
                if (serviceSelect.hasClass('select2-hidden-accessible')) {
                    serviceSelect.select2('destroy');
                }

                // Clear current category options
                categorySelect.empty();
                categorySelect.append('<option value="">Select a Category</option>');

                // Add filtered categories
                let categoryFound = false;
                for (const [id, name] of Object.entries(filteredCategories)) {
                    const isSelected = (currentCategory == id);
                    if (isSelected) categoryFound = true;
                    categorySelect.append(new Option(name, id, false, isSelected));
                }

                // Reinitialize category Select2
                categorySelect.select2();

                // If current category is not in filtered list, clear it
                if (!categoryFound) {
                    categorySelect.val('').trigger('change');
                    serviceSelect.empty();
                    serviceSelect.append('<option value="">Select a service</option>');
                    serviceSelect.select2();
                } else if (currentCategory) {
                    // Update services for the current category
                    updateServicesForCategory(row, currentCategory, currentService);
                }
            });
        }

        // Function to update services dropdown for a specific category
        function updateServicesForCategory(row, categoryId, currentServiceId = null) {
            const serviceSelect = row.find('.serviceSelect');

            // Destroy Select2 if exists
            if (serviceSelect.hasClass('select2-hidden-accessible')) {
                serviceSelect.select2('destroy');
            }

            // Clear current service options
            serviceSelect.empty();
            serviceSelect.append('<option value="">Select a service</option>');

            if (categoryId && tmpSelectedProject && tmpSelectedProject.services) {
                // Filter services based on selected category and project services
                const categoryServices = tmpSelectedProject.services.filter(service =>
                    parseInt(service.category_id) === parseInt(categoryId)
                );

                // Populate service dropdown with filtered services
                categoryServices.forEach(function(projectService) {
                    // Find the service details from global services array
                    const serviceDetails = services.find(s => s.id === projectService.service_id);
                    if (serviceDetails) {
                        const isSelected = (currentServiceId == serviceDetails.id);
                        serviceSelect.append(new Option(serviceDetails.title, serviceDetails.id, false,
                            isSelected));
                    }
                });
            }

            // Reinitialize service Select2
            serviceSelect.select2();

            // Try to restore the previous selection
            if (currentServiceId) {
                setTimeout(() => {
                    if (serviceSelect.find('option[value="' + currentServiceId + '"]').length > 0) {
                        serviceSelect.val(currentServiceId).trigger('change');
                    }
                }, 100);
            }
        }

        $(document).ready(function() {
            // Get the initial row count
            let rowIndex = $('#itemRows tr').length;

            // Filter existing rows immediately if a project is already selected
            const selectedProjectId = $('#projectSelect').val();
            if (selectedProjectId) {
                const selectedProject = projects.find(project => project.id == selectedProjectId);
                if (selectedProject) {
                    tmpSelectedProject = selectedProject;
                    // Filter categories and services for existing rows
                    setTimeout(() => {
                        updateExistingRowsCategoriesAndServices(selectedProject);

                        // Also update rates for existing services
                        $('#itemRows tr').each(function() {
                            const row = $(this);
                            const serviceSelect = row.find('.serviceSelect');
                            const currentService = serviceSelect.val();

                            if (currentService && tmpSelectedProject) {
                                const selectedService = tmpSelectedProject.services.find(service =>
                                    service.service_id == currentService
                                );

                                if (selectedService) {
                                    row.find('.rates').val(selectedService.unit_price || 0);
                                    calculateRow(row);
                                }
                            }
                        });

                        calculateTotals();
                    }, 300);
                }
            }

            // Initialize select2 for existing elements
            $('.serviceSelect, .categorySelect').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2();
                }
            });

            // Calculate for each row on page load (edit page scenario)
            $('#itemRows tr').each(function() {
                calculateRow($(this)); // Calculate each row
            });
            calculateTotals(); // Update overall totals

            $('#projectSelect').change(function() {
                const selectedProjectId = $(this).val();
                updateServiceDropdown(selectedProjectId);
                updateBranches(selectedProjectId);
            });

            function updateServiceDropdown(projectId) {
                const serviceSelect = $('#serviceSelect'); // Make sure this ID matches your HTML
                serviceSelect.empty(); // Clear the current options
                $('#addRow').prop('disabled', true); // Disable add row button initially

                // Find the selected project
                const selectedProject = projects.find(project => project.id == projectId);

                if (selectedProject) {
                    updateTerms(selectedProject.id);
                    if (selectedProject.po_number) {
                        $("#po_number").val(selectedProject.po_number);
                    } else {
                        $("#po_number").val('');
                    }

                    tmpSelectedProject = selectedProject;
                    tmpServices = selectedProject.services; // Get the services for the selected project

                    // Category updates starts here
                    newCategories = [];
                    const categoryIds = [...new Set(selectedProject.services.map(service => service.category_id))];
                    const tmpCategory = {};
                    categoryIds.forEach(id => {
                        if (categories[id]) { // Check if the category exists
                            tmpCategory[id] = categories[id];
                        }
                    });
                    newCategories = tmpCategory;
                    //ends here

                    // Update existing rows with filtered categories and services
                    updateExistingRowsCategoriesAndServices(selectedProject);

                    // Enable the add row button if there are categories available
                    $('#addRow').prop('disabled', Object.keys(newCategories).length === 0);

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
                // Don't add row if no project is selected
                if (!tmpSelectedProject || Object.keys(newCategories).length === 0) {
                    alert('Please select a project first');
                    return;
                }

                let catoptions = '<option value="" selected>Select a Category</option>'; // Default option
                for (const [id, name] of Object.entries(newCategories)) {
                    catoptions += `<option value="${id}">${name}</option>`;
                }

                let row = `
                <tr data-index="${rowIndex}">
                    <td class="p-1 text-center " style="width: 2%;">${rowIndex + 1}</td>
                    <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number" name="itemsArr[${rowIndex}]['item']" value="" readonly></td>
                    <td class="p-1 m-0"  style="width:25%;">
                        <select style="width:100%;" class="form-select categorySelect" name="itemsArr[${rowIndex}]['category_id']" required>
                            ${catoptions}
                        </select>
                    </td>
                    <td class="p-1 m-0"  style="width:25%;">
                        <select style="width: 100%;"  class="form-select serviceSelect" name="itemsArr[${rowIndex}]['service_id']" required style="min-width:150px;">
                            <option value="" selected>Select a service</option>
                            <!-- Options will be populated based on category selection -->
                        </select>
                    </td>
                    <td class="p-1" style="width: 100px;"><input type="number" class="form-control text-right quantity" required value="1" name="itemsArr[${rowIndex}]['quantity']" ></td>
                    <td class="p-1" ><input type="number" class="form-control p-1  text-right rates" required value="0.00" name="itemsArr[${rowIndex}]['rate']" style="width: 100px !important;" readonly></td>
                    <td class="p-0"><input type="number" class="form-control text-right discount " required value="0.00" name="itemsArr[${rowIndex}]['discount']" style="width:90px;background:white;border:none;" readonly></td>
                    <td class="p-1 m-0" style="width: 10%;"><input type="number" class="form-control text-right taxable" value="0.00" name="itemsArr[${rowIndex}]['taxable']" readonly style="background:white;border:none;"></td>

                    <td class="p-0"><input type="number" class="form-control  text-right tax" value="15.00"  name="itemsArr[${rowIndex}]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                    <td class="vat-amount text-end pl-5" >0.00</td>

                    <td class="p-0 pr-2"><input type="number" readonly class="total-amount  form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0.00" style="background:white;border:none;width:90px;" ></td>
                    <td class="p-1 text-right"><button type="button" class="btn text-danger remove-row"><i class="fa fa-times"></i></button></td>
                </tr>
            `;
                $('#itemRows').append(row);
                rowIndex++;

                // Initialize Select2 for new row
                $('#itemRows tr:last .categorySelect, #itemRows tr:last .serviceSelect').select2();
            }

            $(document).on('change', '.serviceSelect', function() {
                var selectedValue = $(this).val(); // Get the selected value

                if (tmpSelectedProject && selectedValue) {
                    var selectedService = tmpSelectedProject.services.find(service => service.service_id ==
                        selectedValue);

                    if (selectedService) {
                        var itemNumber = selectedService.ref_no;
                        var descriptionText = $('<div>').html(selectedService.description || '').text();
                        let row = $(this).closest('tr');
                        row.find('textarea[name*="description"]').val(descriptionText || '');
                        row.find('.item-number').val(itemNumber || '');
                        row.find('.rates').val(selectedService.unit_price || 0);

                        calculateRow(row);
                        calculateTotals();
                    }
                }
            });

            $(document).on('change', '.categorySelect', function() {
                var selectedCategory = $(this).val();
                var row = $(this).closest('tr');
                const currentService = row.find('.serviceSelect').val();

                // Update services for this category
                updateServicesForCategory(row, selectedCategory, currentService);
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

                // Check for Infinity or NaN
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

            // Add event listeners for deduction fields
            $(document).on('input', '.deduction-field', function() {
                calculateTotals();
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
    </script>


    {{-- <script>
        let rowIndex = 0; // To keep track of the row index

        function updateBranches(projectId) {
            // Clear the existing options in the branch select box
            $('#branchSelect').empty();

            const selectedProject = allProjects.find(project => project.id == projectId);
            if (selectedProject && selectedProject.branch_id) {
                // Add the branch corresponding to the project's branch_id
                const branchId = selectedProject.branch_id;
                if (allBranches[branchId]) {
                    $('#branchSelect').append('<option value="' + branchId + '">' + allBranches[branchId] +
                        '</option>');

                    // var nextNumber = "{{ $invoice->invoice_number }}";
                    // nextNumber = nextNumber.split('/')[1];
                    // if (branchId) {
                    //     // Update the invoiceNumber field with the format "branch_id/nextNumber"
                    //     $('#invoiceNumber').val(branchId + '/' + nextNumber);
                    // } else {
                    //     // Clear the invoiceNumber field if no branch is selected
                    //     $('#invoiceNumber').val('');
                    // }
                }
            }
        }

        $(document).ready(function() {

            let rowIndex = $('#itemRows tr').length; // Set the row index based on the existing number of rows
            calculateTotals();
            $('.serviceSelect').select2(); // Initialize select2 for existing elements

            // Calculate for each row on page load (edit page scenario)
            $('#itemRows tr').each(function() {
                calculateRow($(this)); // Calculate each row
            });
            calculateTotals(); // Update overall totals

            $('#projectSelect').change(function() {

                const selectedProjectId = $(this).val();
                updateServiceDropdown(selectedProjectId);
                updateBranches(selectedProjectId);


            });



            function updateServiceDropdown(projectId) {
                const serviceSelect = $('#serviceSelect'); // Make sure this ID matches your HTML
                serviceSelect.empty(); // Clear the current options
                $('#addRow').prop('disabled', true); // Disable add row button initially

                // Find the selected project
                const selectedProject = projects.find(project => project.id == projectId);

                if (selectedProject) {
                    updateTerms(selectedProject.id);
                    if (selectedProject.po_number) {
                        $("#po_number").val(selectedProject.po_number);
                    } else {
                        $("#po_number").val('');
                    }

                    tmpServices = selectedProject.services; // Get the services for the selected project

                    // Filter services based on the selected project
                    const filteredServices = services.filter(service =>
                        tmpServices.some(tmpService => tmpService.service_id === service.id)
                    );



                    // Populate the service dropdown
                    filteredServices.forEach(service => {
                        serviceSelect.append(new Option(`${service.title} - Price: ${service.rate}`, service
                            .id));
                    });

                    //category updates starts here
                    newCategories = [];
                    const categoryIds = [...new Set(selectedProject.services.map(service => service.category_id))];
                    const tmpCategory = {};
                    categoryIds.forEach(id => {
                        if (categories[id]) { // Check if the category exists
                            tmpCategory[id] = categories[id];
                        }

                    });
                    newCategories = tmpCategory;
                    //ends here

                    // Enable the add row button if there are available services
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
                    <td class="p-1 text-center " style="width: 2%;">${rowIndex + 1}</td>
                    <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number" name="itemsArr[${rowIndex}]['item']" value="" readonly></td>
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
                    <td class="p-1" ><input type="number" class="form-control p-1  text-right rates" required value="0.00" name="itemsArr[${rowIndex}]['rate']" style="width: 100px !important;" readonly></td>
                    <td class="p-0"><input type="number" class="form-control text-right discount " required value="0.00" name="itemsArr[${rowIndex}]['discount']" style="width:90px;background:white;border:none;" readonly></td>
                    <td class="p-1 m-0" style="width: 10%;"><input type="number" class="form-control text-right taxable" value="0.00" name="itemsArr[${rowIndex}]['taxable']" readonly style="background:white;border:none;"></td>

                    <td class="p-0"><input type="number" class="form-control  text-right tax" value="15.00"  name="itemsArr[${rowIndex}]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                    <td class="vat-amount text-end pl-5" >0.00</td>

                    <td class="p-0 pr-2"><input type="number" readonly class="total-amount  form-control text-right" name="itemsArr[${rowIndex}]['total']" value="0.00" style="background:white;border:none;width:90px;" ></td>
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



                row.find('textarea[name*="description"]').val(descriptionText ||
                    ''); // Set the description in the textarea
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
                        console.log(new Option(service.title, service.id), service);
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
    </script> --}}
    <script>
        $(document).on('click', '#editSaveAsDraft', function(e) {
            e.preventDefault();
            $('#invoiceStatus').val($(this).data('status')); // 0 = draft
            $('#forceTodayDate').val(0); // don't force today's date
            $('#invoiceForm').submit();
        });

        $(document).on('click', '#editSaveSend', function(e) {
            e.preventDefault();
            $('#invoiceStatus').val($(this).data('status')); // 1 = invoice
            $('#forceTodayDate').val(1); // force today's date
            $('#invoiceForm').submit();
        });
    </script>
@endsection
