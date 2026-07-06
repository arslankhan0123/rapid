@extends('layouts.app')
@section('title')
    {{ __('messages.payment_voucher.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .readonly-select {
            pointer-events: none;
            background-color: #e9ecef;
            /* Optional: Change background to look like a disabled input */
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1> {{ __('messages.payment_voucher.add') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('expenses.index') }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        @include('layouts.errors')
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'expenses.store', 'id' => 'createExpense', 'files' => true]) }}

                    @include('expenses.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
    @include('expenses.expense_category_modal')
    @include('payment_modes.common_payment_mode')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let isEdit = false;
    </script>
    <script src="{{ mix('assets/js/expenses/create-edit.js') }}"></script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/file-attachments/attachment.js') }}"></script>

    <script>
        var employees = @json($employees);



        $(document).ready(function() {
            // Initialize Select2
            $('#employeeSelect').select2();
            // On change of the select dropdown
            $('#employeeSelect').on('change', function() {
                // Get the selected option's value and text
                let selectedValue = $(this).val();
                let selectedEmployeeName = $(this).find('option:selected').text();

                // Update the input field only if a valid value is selected
                if (selectedValue) {
                    $('#employeeInmput').val(selectedEmployeeName);
                } else {
                    $('#employeeInmput').val(''); // Clear the input if no valid selection
                }
            });

            // Listen for the change event on the branch dropdown
            // $('#filterBranch').on('change', function() {
            //     var branchId = $(this).val(); // Get the selected branch ID

            //     // Filter employees based on the selected branch
            //     var filteredEmployees = employees.filter(function(employee) {
            //         return employee.branch_id == branchId;
            //     });

            //     // Populate the employee select dropdown
            //     var employeeSelect = $('#employeeSelect');
            //     employeeSelect.empty(); // Clear previous options
            //     employeeSelect.append('<option value="">Select Employee</option>'); // Placeholder

            //     // Add the filtered employees to the select dropdown
            //     filteredEmployees.forEach(function(employee) {
            //         employeeSelect.append('<option value="' + employee.id + '">' + employee.name +
            //             '</option>');
            //     });

            //     // Reinitialize select2 to ensure it works after dynamic content change
            //     employeeSelect.trigger('change');
            // });



            let suppliers = @json($suppliers); // Convert Laravel collection to JSON
            $('#selectSupplier').change(function() {
                let supplierId = $(this).val(); // Get selected supplier ID
                let vatNumber = ''; // Default empty VAT number

                if (supplierId) {
                    let selectedSupplier = suppliers.find(s => s.id == supplierId); // Find supplier by ID
                    vatNumber = selectedSupplier ? selectedSupplier.vat_number : ''; // Get VAT number
                    $('#supp_vat_number').val(vatNumber).prop('readonly', true);
                } else {
                    $('#supp_vat_number').val('').prop('readonly', false);
                }


            });



            $('#addField').click(function() {
                var newField = `
            <div class="form-group row mb-3">
                <div class="col-md-5">
                    <label for="doc_name" class="form-label">Name:</label>
                    <input type="text" name="doc_name[]" class="form-control" >
                </div>
                <div class="col-md-6">
                    <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }}, {{ __('messages.employees.max_size') }}</label>
                    <input type="file" name="file[]" class="form-control" accept="application/pdf" >
                </div>
                <div class="col-md-3  d-none">
                    <label for="expiry_date" class="form-label">Expiry Date:</label>
                    <input type="date" name="expiry_date[]" class="form-control" >
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger removeField"><i class="fa fa-minus" aria-hidden="true"></i></button>
                </div>
            </div>`;

                $('#formContainer').append(newField);
            });

            // Remove a form field
            $(document).on('click', '.removeField', function() {
                $(this).closest('.form-group').remove();
            });

        });
    </script>


    <script>
        $(document).ready(function() {
            $('#expenseCategory').change(function() {
                const categoryId = $(this).val();
                const subCategoryDropdown = $('#subCategory');

                // Clear the subcategory dropdown
                subCategoryDropdown.empty().append(
                    $('<option>', {
                        value: '',
                        text: 'Select Sub Category',
                    })
                );

                // Check if a category is selected
                if (categoryId) {
                    // Filter subcategories using the data already available on the page
                    const subCategories = @json($subCategories);
                    const filteredSubCategories = subCategories.filter(
                        subCategory => subCategory.expense_category_id == categoryId
                    );

                    // Populate the dropdown with filtered subcategories
                    $.each(filteredSubCategories, function(key, subCategory) {
                        subCategoryDropdown.append(
                            $('<option>', {
                                value: subCategory.id,
                                text: subCategory.name,
                            })
                        );
                    });
                }
            });
        });
    </script>
    <script>
        // Simulated accounts data
        const allAccounts = @json($accounts);


        // On branch change
        $('#filterBranch').on('change', function() {
            const selectedBranchId = $(this).val(); // Get selected branch_id

            // Filter accounts based on branch_id
            const filteredAccounts = allAccounts.filter(account => account.branch_id == selectedBranchId);

            // Clear the existing payment mode options
            const paymentModeSelect = $('#paymentModeNew');
            paymentModeSelect.empty();

            // Add placeholder option
            paymentModeSelect.append(
                $('<option>', {
                    value: '',
                    text: "{{ __('messages.placeholder.select_payment_mode') }}",
                })
            );

            // Add filtered accounts to the payment mode dropdown
            filteredAccounts.forEach(account => {
                paymentModeSelect.append(
                    $('<option>', {
                        value: account.id,
                        text: account.account_name,
                    })
                );
            });

            // Trigger select2 refresh if you're using select2
            paymentModeSelect.trigger('change');
        });
    </script>
@endsection
