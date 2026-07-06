@extends('layouts.app')
@section('title')
    {{ __('messages.projects') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Add this CSS to your styles file or within a <style> tag in your Blade view */
        .modal-dialog {
            max-width: 800px;
            /* Set the maximum width of the modal */
        }

        .modal-body {
            max-height: 400px;
            /* Set a maximum height for the modal body */
            overflow-y: auto;
            /* Enable vertical scrolling */
        }
    </style>
@endsection
@section('css')
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.project.edit_project') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('projects.index') }}"
                    class="btn btn-primary form-btn float-right-mobile">{{ __('messages.common.list') }}</a>
            </div>
        </div>
        @include('layouts.errors')
        <div class="section-body">
            {{ Form::open(['route' => ['projects.update', $project->id], 'method' => 'put', 'id' => 'editProject']) }}
            <div class="card">
                <div class="card-body">
                    @include('projects.fields')
                    @include('projects.edit_employee')
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <div class="col-md-6 p-0 m-0">
                                <label for="terms_dropdown">Select Terms & Conditions:</label>
                                <select class="form-control" id="terms_dropdown">
                                    <option value=""></option>
                                    @foreach ($terms as $key => $term)
                                        <option value="{{ $key }}">{{ strip_tags($term) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-3">
                                <table class="table table-bordered" id="terms_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px;">SL</th>
                                            <th>Description</th>
                                            <th style="width: 20px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Existing terms will be dynamically populated here on page load -->
                                        @foreach ($project->terms as $index => $estimateTerm)
                                            <tr data-id="{{ $estimateTerm['terms_id'] }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td class="p-0 m-0">
                                                    <textarea name="termDescription[]" class="form-control" style="height:120px;width:100%;">{{ $estimateTerm['description'] }}</textarea>
                                                    <input type="hidden" name="terms[]"
                                                        value="{{ $estimateTerm['terms_id'] }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm delete-row"><i
                                                            class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row justify-content-end mr-3 mt-2">
                                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            {{ Form::close() }}
        </div>
    </section>
    @include('projects.add_customer_modal')
    @include('tags.common_tag_modal')
    @include('manual-sales.modal_create_item')
@endsection
@section('page_scripts')
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/moment.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let editData = true;
        let editContactIds = JSON.parse(
            '{{ isset($data['projectContacts']) && count($data['projectContacts']) > 0 ? json_encode($data['projectContacts']) : 0 }}'
        );
    </script>
    <script src="{{ mix('assets/js/projects/new.js') }}"></script>

    <script>
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
                        $('.category-select').last().trigger('change'); // reset select2

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
            
            
            
            
            
            // Handle the tab shown event
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                // Remove the bg-primary and text-white classes from all tabs
                $('a[data-toggle="tab"]').removeClass('bg-primary text-white');

                // Add the bg-primary and text-white classes to the active tab
                $(e.target).addClass('bg-primary text-white');
            });
        });
        
        
        $(document).on('change', '.category-select', function() {
            var selectedCategory = $(this).val();
            var row = $(this).closest('tr');

            var serviceSelect = row.find('.service-select');

            $.ajax({
                url: "{{ route('item-services.services') }}",
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
        });
    </script>

    <script>
        $(document).ready(function() {
            let termIndex = $('#terms_table tbody tr').length + 1; // Start index based on existing rows

            // Add selected term to the table
            $('#terms_dropdown').change(function() {
                let selectedValue = $(this).val();
                let selectedText = $('#terms_dropdown option:selected').text();

                if (selectedValue && selectedText) {
                    // Check if the term is already added
                    if ($('#terms_table tbody').find(`tr[data-id="${selectedValue}"]`).length === 0) {

                        $('#terms_table tbody').append(`
                        <tr data-id="${selectedValue}" class='mt-1'>
                            <td>${termIndex}</td>
                            <td class='p-0 m-0'> <textarea name="termDescription[]" class='form-control' style='height:120px;width:100%;' >${selectedText}</textarea><input type='hidden' name='terms[]' value="${selectedValue}" </td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
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
        });
    </script>


    <script>
        $(document).ready(function() {
            // Initialize Select2
            function initializeSelect2() {
                $('.iqama-select').select2({
                    width: '100%',
                    allowClear: true
                });
            }

            initializeSelect2(); // Call on page load

            // Function to populate Employee Name and Designation
            function populateEmployeeDetails(select) {
                let selectedOption = select.find(':selected');
                let name = selectedOption.data('name');
                let designation = selectedOption.data('designation');
                select.closest('tr').find('.employee-name').val(name);
                select.closest('tr').find('.designation').val(designation);
            }

            // Handle change event for Iqama selection
            $(document).on('change', '.iqama-select', function() {
                populateEmployeeDetails($(this));
            });

            // Add new row
            $('#addRowBtn').click(function() {
                let newRowIndex = $('#employeeTable tbody tr').length + 1;

                let newRow = `
            <tr>
                <td>${newRowIndex}</td>
                <td>
                    <select name="members[]" class="form-control iqama-select" required>
                        <option value="">Select Iqama No</option>
                        @foreach ($data['members'] as $member)
                            <option value="{{ $member['id'] }}" data-name="{{ $member['name'] }}" data-designation="{{ $member['designation']->name ?? '' }}">
                                {{ $member['iqama_no'] }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" class="form-control employee-name" readonly></td>
                <td><input type="text" class="form-control designation" readonly></td>
                <td><input type="number" name="hourly_rate[]" class="form-control hourly-rate" required></td>
                <td>
                    <button type="button" class="btn btn-danger deleteRowBtn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;

                $('#employeeTable tbody').append(newRow);
                initializeSelect2(); // Reinitialize Select2 for new rows
            });

            // Delete row
            $(document).on('click', '.deleteRowBtn', function() {
                console.log("Delete button clicked");
                $(this).closest('tr').remove();
                updateRowNumbers();
            });

            // Update row numbers
            function updateRowNumbers() {
                $('#employeeTable tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Populate details for existing rows
            $('#employeeTable tbody tr').each(function() {
                let select = $(this).find('.iqama-select');
                if (select.length) {
                    populateEmployeeDetails(select);
                }
            });
        });
    </script>

    {{-- getting customer --}}

    <script>
        $(document).ready(function() {
            // Encode the states data into a JavaScript object
            var statesData = @json($states->groupBy('country_id'));

            // Function to update states based on the selected country
            function updateStates() {
                var selectedCountryId = $('#billingCountryId').val();
                var $stateSelect = $('#billingState');
                $stateSelect.empty(); // Clear existing options

                // Check if there are states for the selected country
                if (statesData[selectedCountryId]) {
                    $.each(statesData[selectedCountryId], function(index, state) {
                        $stateSelect.append($('<option>', {
                            value: state.id,
                            text: state.name
                        }));
                    });
                }
            }

            // Event listener for country change
            $('#billingCountryId').change(updateStates);

            // Initial state update
            updateStates();
        });
    </script>
    <script>
        var allCustomer = [];
        $(document).ready(function() {
            $('.iqama-select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#terms_dropdown').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });

            $('#customerSelectBox').select2({
                allowClear: true // Allow clearing the selection
            });
            setCustomers();
            $("#btnAddCustomer").click(function() {
                $("#customeraddModal").modal('show');
            });

            $('#customerSelectBox').on('change', function() {
                var selectedCustomerId = $(this).val();
                var selectedCustomer = allCustomer.find(function(customer) {
                    return customer.id == selectedCustomerId;
                });
                if (selectedCustomer) {
                    $("#customer_code").val(selectedCustomer.code);
                    $("#vendor_code").val(selectedCustomer.vendor_code??'');
                }
            });

        });

        function setCustomers() {
            // Fetch customers on page load or based on an event (e.g., selecting a project)
            $.ajax({
                url: "{{ route('projects.customers') }}", // The route defined to get customers
                type: "GET",
                processData: true, // Disable data processing
                cache: false,
                dataType: "json",
                success: function(data) {
                    allCustomer = data;
                    let $customerSelectBox = $('#customerSelectBox');
                    $customerSelectBox.empty(); // Clear any existing options
                    // Add a placeholder option
                    // Add placeholder option first
                    $customerSelectBox.append('<option value="">' +
                        '{{ __('messages.placeholder.select_customer') }}' + '</option>');
                    // Populate with new options in reverse order
                    $.each(data, function(id, customer) {
                        // Prepend the option instead of appending
                        $customerSelectBox.append('<option value="' + customer.id + '">' + customer
                            .company_name +
                            '</option>');
                    });
                    // If needed, you can also set a default selected value here
                    let selectedCustomerId =
                        "{{ isset($project->customer_id) ? $project->customer_id : $customerId }}";
                    if (selectedCustomerId) {
                        $customerSelectBox.val(selectedCustomerId);
                    }
                },
                error: function() {
                    console.log('Error retrieving customer data.');
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission
                processingBtn('#addCustomerForm', '#btnCustomerSave', 'loading');
                $.ajax({
                    url: '{{ route('customers.store') }}', // URL to submit to
                    type: 'POST', // POST method
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        processingBtn('#addCustomerForm', '#btnCustomerSave');
                        setCustomers();
                        displaySuccessMessage("Customer Added Successfully");
                        $("#customeraddModal").modal('hide');
                    },
                    error: function(xhr, status, error) {
                        processingBtn('#addCustomerForm', '#btnCustomerSave');
                        $("#customeraddModal").modal('hide');

                    }
                });
            });
        });
    </script>

    <script>
        var tmpServiceId = null;
        $(document).ready(function() {
            // Add event listener to handle changes in the service select
            $('#itemRows').on('change', '.service-select', function() {
                var row = $(this).closest('tr'); // Get the closest row
                var selectedService = $(this).find(':selected'); // Get the selected option
                var categoryId = selectedService.data('category-id'); // Get the associated category ID
                var serviceId = $(this).val();

                var categorySelect = row.find(
                    '.category-select'); // Find the category select in the same row
                tmpServiceId = serviceId;
                // If a category is not selected, update the category select
                if (!categorySelect.val()) {
                    categorySelect.val(categoryId).trigger(
                        'change'); // Set the category select to the service's category
                }


            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var services = @json($services);

            function generateNewRow(rowIndex) {
                return `
            <tr>
                <td>${rowIndex}</td>
                <td  style="width: 20%;" class="p-1">
                    <input type="text" class="form-control" name="ref_no[]" required>
                </td>
                <td class="p-1" style="width: 25%;">
                    <select class="form-control category-select select2" name="category_id[]" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="p-1" style="width: 25%;">
                    <select class="form-control service-select select2" name="service_id[]" required>
                        <option value="">Select Service</option>
                          @foreach ($services as $id => $name)
                                <option value="{{ $name['id'] }}" data-category-id="{{ $name['item_group_id'] }}" >{{ $name['title'] }}</option>
                          @endforeach
                    </select>
                </td>
                <td class="p-1">
                    <input type="number" class="form-control text-right" name="unit_price[]" required step="any">
                </td>
                <td class="p-1">
                    <button type="button" class="btn text-danger remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            }

            // Function to populate services based on selected category
            function updateServiceOptions(row) {
                var categoryId = row.find('.category-select').val();
                var serviceSelect = row.find('.service-select');

                serviceSelect.empty();
                serviceSelect.append('<option value="">Select Service</option>');

                // Filter and add services that belong to the selected category
                $.each(services, function(index, service) {

                    if (service.item_group_id == categoryId) {

                        var selected = '';
                        if (tmpServiceId && tmpServiceId == service.id) {
                            serviceSelect.append('<option value="' + service.id + '" selected>' + service
                                .title +
                                '</option>');
                        } else {
                            serviceSelect.append('<option value="' + service.id + '">' + service.title +
                                '</option>');
                        }
                        // serviceSelect.append('<option value="' + service.id + '">' + service.title +
                        //     '</option>');
                    }
                });
                tmpServiceId = null;
                // Re-initialize Select2 for the service dropdown after updating options
                serviceSelect.select2();
            }

            // Add event listener to handle dynamic rows
            $('#itemRows').on('change', '.category-select', function() {
                var row = $(this).closest('tr');
                updateServiceOptions(row);
            });

            // Add new row dynamically and reinitialize Select2
            $('.add-row').click(function() {
                var newRow = generateNewRow($('#itemRows tr').length + 1);
                $('#itemRows').append(newRow);

                // Initialize Select2 on the newly added row's selects
                $('#itemRows .select2').select2();
            });

            // Remove row
            $('#itemRows').on('click', '.remove-row', function() {
                if ($('#itemRows tr').length > 1) {
                    $(this).closest('tr').remove();
                }

                // Update serial numbers after row removal
                $('#itemRows tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });

            // Initialize Select2 on the first row on page load
            $('#itemRows .select2').select2();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('input, select, textarea').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent form submission on Enter
                    let form = $(this).closest('form');
                    let inputs = form.find('input, select, textarea');
                    let index = inputs.index(this);

                    // Check if the next field is Select2
                    if ($(inputs[index + 1]).hasClass('select2')) {
                        // Listen for Select2 selection
                        $(inputs[index + 1]).on('select2:select', function() {
                            // Focus the next input field after selection
                            inputs.eq(index + 2).focus();
                        });
                    } else if ($(inputs[index + 1]).hasClass('summernote-simple')) {
                        // Focus Summernote editor
                        $(inputs[index + 1]).summernote('focus');
                    } else {
                        // Focus the next input field
                        inputs.eq(index + 1).focus();
                    }
                }
            });

            // Handle Enter in Select2 field to trigger focus on the next field
            $('.select2').on('select2:select', function(e) {
                let form = $(this).closest('form');
                let inputs = form.find('input, select, textarea');
                let index = inputs.index(this);
                inputs.eq(index + 1).focus();
            });
        });
    </script>
@endsection
