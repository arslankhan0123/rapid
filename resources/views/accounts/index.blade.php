@extends('layouts.app')
@section('title')
    {{ __('messages.accounts.accounts') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        .modal-backdrop {
            display: none;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.accounts.accounts') }} </h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_leave_groups')
                <div class="float-right">
                    {{ Form::select('cash_type', ["Main Cash"=>"Main Cash",'Petty Cash'=>"Petty Cash",'Bank Transfer'=>"Bank Transfer"], null, ['id' => 'filterCash', 'class' => 'form-control select2', 'placeholder' => "Cash Name"]) }}

                    {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}

                    {{-- <a href="{{ route('accounts.create') }}" class="btn btn-primary ml-2" style="line-height: 30px;">
                        {{ __('messages.accounts.transfer_cash') }} </a> --}}
                    <button class="btn btn-primary ml-2" style="line-height: 30px;" id="btnCashTransfer">
                        {{ __('messages.accounts.transfer_cash') }} </button>
                </div>
            @endcan
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('flash_notification'))
                        @foreach (session('flash_notification') as $message)
                            <div class="alert alert-{{ $message['level'] }}">
                                {{ $message['message'] }}
                            </div>
                        @endforeach
                    @endif
                    <div class="row d-none">
                        <!-- Payment Mode Card -->
                        <div class="col mb-2">
                            <div class="card"
                                style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 8px; transition: transform 0.2s ease-in-out; transform: translateY(0px);"
                                onmouseover="this.style.transform='translateY(-10px)';this.style.boxShadow='0 10px 20px rgba(0, 0, 0, 0.1)';"
                                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">

                                <!-- Card Header with Dynamic Payment Mode Name -->
                                <div class="card-header"
                                    style="background-color:
                     #28ace2;
                                        color: white; font-size: 1.25rem;">

                                    <p class="aname_1"></p>
                                </div>
                                <!-- Card Body with Dynamic Total Amount -->
                                <div class="card-body text-center" style="font-size: 1.5rem; font-weight: bold;">
                                    <h3 class="avalue_1">0.00</h3>
                                </div>
                            </div>
                        </div>
                        <!-- Payment Mode Card -->
                        <div class="col mb-2">
                            <div class="card"
                                style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease-in-out;"
                                onmouseover="this.style.transform='translateY(-10px)';this.style.boxShadow='0 10px 20px rgba(0, 0, 0, 0.1)';"
                                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">

                                <!-- Card Header with Dynamic Payment Mode Name -->
                                <div class="card-header"
                                    style="background-color:
                     orange;                     color: white; font-size: 1.25rem;">

                                    <p class="aname_2"></p>
                                </div>
                                <!-- Card Body with Dynamic Total Amount -->
                                <div class="card-body text-center" style="font-size: 1.5rem; font-weight: bold;">
                                    <h3 class="avalue_2">0.00</h3>
                                </div>
                            </div>
                        </div>
                        <!-- Payment Mode Card -->
                        <div class="col mb-2">
                            <div class="card"
                                style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 8px; transition: transform 0.2s ease-in-out; transform: translateY(0px);"
                                onmouseover="this.style.transform='translateY(-10px)';this.style.boxShadow='0 10px 20px rgba(0, 0, 0, 0.1)';"
                                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">

                                <!-- Card Header with Dynamic Payment Mode Name -->
                                <div class="card-header"
                                    style="background-color:
                     #f5365c;
                                        color: white; font-size: 1.25rem;">

                                    <p class="aname_3"></p>
                                </div>
                                <!-- Card Body with Dynamic Total Amount -->
                                <div class="card-body text-center" style="font-size: 1.5rem; font-weight: bold;">
                                    <h3 class="avalue_3">0.00</h3>
                                </div>
                            </div>
                        </div>


                    </div>
                    @include('accounts.cashTransferModal')
                    @include('accounts.modal')
                    @include('accounts.table')
                </div>
            </div>
        </div>
    </section>
    @include('accounts.templates.templates')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <!-- DataTables and Buttons Extension JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <!-- pdfmake for PDF export -->
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        let tbl = $('#designationTable').DataTable({
            oLanguage: {
                'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                'sInfo': Lang.get('messages.common.data_base_entries'),
                sLengthMenu: Lang.get('messages.common.menu_entry'),
                sInfoEmpty: Lang.get('messages.common.no_entry'),
                sInfoFiltered: Lang.get('messages.common.filter_by'),
                sZeroRecords: Lang.get('messages.common.no_matching'),
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: route('accounts.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                    d.account_name=$("#filterCash").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'branch.name',
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.account_number;
                        return element.value;
                    },
                    name: 'account_number',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.account_name;
                        return element.value;
                    },
                    name: 'account_name',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.received_by?.name ?? '';
                        return element.value;
                    },
                    name: 'receivedBy.name',

                }, {
                    data: function(row) {
                        let element = document.createElement('p');
                        let formattedAmount = parseFloat(row.opening_balance).toFixed(
                            2); // Format the amount to 2 decimal places
                        element.textContent = formattedAmount;
                        element.classList.add('text-right'); // Add class to the span for right-alignment
                        return element.outerHTML;
                    },
                    name: 'opening_balance',
                    width: '25%'
                },
                {
                    data: function(row) {
                        return `
                            <button title="Pay" data-id="${row.id}" data-branch-id="${row.branch_id}"
                                class="btn btn-warning action-btn has-icon pay-btn"
                                style="width:50px; float:right;">
                                Add
                            </button>
                            <button title="Edit" data-id="${row.id}"
                                class="btn btn-info action-btn has-icon edit-btn-cash"  data-id="${row.id}" data-branch-id="${row.branch_id}" data-balance="${row.opening_balance??0}" data-date="${row.date??''}"
                                style="width:50px; float:right; margin-right:5px;">
                                Edit
                                </button>
                            `;
                    },
                    name: 'id',
                    width: '14%'
                }
            ],


            responsive: true,
            dom: "Bfrtip", // Ensure Buttons (B) is part of the DOM structure
            buttons: [
                @can('export_accounts')
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn btn-sm',
                        title: 'Accounts', // Set the custom file name here
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 5; // Exclude the action column at index 4
                            }
                        }
                    }, {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-sm',
                        title: 'Accounts',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 5; // Exclude the action column at index 4
                            }
                        }
                    }, {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        className: 'btn btn-sm', // Styled button
                        orientation: 'portrait',
                        title: 'Accounts',
                        pageSize: 'A4',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 5; // Exclude the action column at index 3
                            }
                        },
                        customize: function(doc) {
                            // Optional customization of the PDF
                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                            // Right-align the last column
                            var lastColumnIndex = doc.content[1].table.body[0].length -
                                1; // Get index of the last column
                            doc.content[1].table.body.forEach(function(row, rowIndex) {
                                if (rowIndex > 0) { // Skip the header row
                                    row[lastColumnIndex].alignment = 'right';
                                }
                            });
                        }
                    }
                @endcan
            ],
            pageLength: 10
        });

        $('#filterBranch,#filterCash').change(function() {

            tbl.ajax.reload(); // Reload DataTable with new status filter
            getFilteredData();
        });
        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('accounts.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('accounts.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.accounts.name') }}');
        });
        $(document).on('click', '#btnCashTransfer', function(event) {
            $("#cashTransferModal").modal('show');
        });
    </script>
    <script>
        // Define messages for translations
        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            edit: "{{ __('messages.common.edit') }}",
            view: "{{ __('messages.common.view') }}"
        };

        // Define permissions
        var permissions = {
            // updateItem: "{{ auth()->user()->can('update_accounts') ? 'true' : 'false' }}",
            // deleteItem: "{{ auth()->user()->can('delete_accounts') ? 'true' : 'false' }}",
            // viewItem: "{{ auth()->user()->can('view_accounts') ? 'true' : 'false' }}"
            updateItem: false,
            deleteItem: false,
            viewItem: false
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('accounts.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('accounts.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteItem === 'true') {
                buttons += `
                <a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }


            return buttons;
        }
    </script>

    <script>
        // Method to trigger AJAX call based on selected filterBranch\
        getFilteredData();

        function getFilteredData() {

            const filterBranchValue = $('#filterBranch').val() ?? null;
            const accountId = $('#accountId').val(); // Replace with the actual way you get the ID
            const url = route('accounts.card', {
                id: filterBranchValue
            });

            // Perform the AJAX call
            $.ajax({
                url: url, // The URL includes the ID in the route
                method: 'GET', // Use GET method
                success: function(response) {
                    // Handle the success response
                    console.log('Filtered data:', response);
                    // Process and display the data here (e.g., populate a table, etc.)
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error('Error fetching filtered data:', error);

                }
            });
        }
    </script>

    <script>
        function progressBTN(button, isLoading) {
            if (isLoading) {
                button.prop('disabled', true); // Disable the button
                button.html('Sending... <i class="fa fa-spinner fa-spin"></i>'); // Show loading spinner
            } else {
                button.prop('disabled', false); // Enable the button
                button.html('Submit'); // Reset button text
            }
        }
        // Pass PHP data to JavaScript as a JSON object
        const accountsData = @json($accounts);
        $(document).ready(function() {
            const accountsByBranch = {};

            // Prepare accounts by branch
            accountsData.forEach(account => {
                if (!accountsByBranch[account.branch_id]) {
                    accountsByBranch[account.branch_id] = [];
                }
                accountsByBranch[account.branch_id].push({
                    id: account.id,
                    name: account.account_name,
                });
            });

            // Handle branch selection change
            $('#branchSelect').change(function() {
                const branchId = $(this).val();
                const filteredAccounts = accountsByBranch[branchId] || [];

                // For the from_account dropdown, filter to only include "Main Cash"
                var mainCashAccount = filteredAccounts.filter(function(account) {
                    return account.name === 'Main Cash';
                });
                var prettyCash = filteredAccounts.filter(function(account) {
                    return account.name === 'Petty Cash';
                });
                updateDropdown('#from_account', mainCashAccount);
                updateDropdown('#to_account', prettyCash);
            });

            function updateDropdown(selector, accounts) {
                const dropdown = $(selector);
                dropdown.empty();

                accounts.forEach(account => {
                    dropdown.append(`<option value="${account.id}">${account.name}</option>`);
                });
            }
        });


        $('#btnEditSave').on('click', function(e) {
            e.preventDefault();
            var branchId = $('#branchSelect').val();
            var fromAccount = $('#from_account').val();
            var toAccount = $('#to_account').val();
            var transferAmount = $('#transfer_amount').val();

            // Check if any required fields are missing
            if (!branchId || !fromAccount || !toAccount || !transferAmount) {
                alert('Please fill out all required fields.');
                return;
            }

            var formData = {
                branch_id: branchId,
                from_account: fromAccount,
                to_account: toAccount,
                transfer_amount: transferAmount,

            }
            progressBTN($('#btnEditSave'), true);

            $.ajax({
                url: "{{ route('accounts.cash-transfer') }}",
                type: 'POST',
                data: formData,
                success: function(response) {

                    if (response.status == true) {
                        tbl.ajax.reload();
                        displaySuccessMessage(response.message);
                        $("#transfer_amount").val('');
                    } else {
                        displayErrorMessage(response.message);
                    }
                    $("#cashTransferModal").modal('hide');
                    progressBTN($('#btnEditSave'), false);
                },
                error: function(error) {
                    //console.log(error);
                    $("#cashTransferModal").modal('hide');
                    displayErrorMessage(error.message);
                    progressBTN($('#btnEditSave'), false);

                }
            });

        });


        $(document).on('click', '.pay-btn', function() {
            var accountId = $(this).data('id');
            var branchId = $(this).data('branch-id');

            // Open the modal
            $('#payAccountModal').modal('show');

            // You can store the accountId and branchId in hidden fields if needed
            $('#payAccountForm').find('input[name="account_id"]').val(accountId);
            $('#payAccountForm').find('input[name="branch_id"]').val(branchId);
        });

        $(document).on('click', '.edit-btn-cash', function() {
            var accountId = $(this).data('id');
            var branchId = $(this).data('branch-id');
            var balance = $(this).data('balance');
            var date = $(this).data('date');
            // Open the modal
            $('#updatePayAccountModal').modal('show');

            // // You can store the accountId and branchId in hidden fields if needed
            $('#payAccountFormUpdate').find('input[name="account_id"]').val(accountId);
            $('#payAccountFormUpdate').find('input[name="branch_id"]').val(branchId);
            $("#update_pay_amount").val(balance);
            $("#update_input_date").val(date);

        });

        $('#payAccountForm').on('submit', function(e) {
            e.preventDefault();

            var amount = $('#pay_amount').val();
            var accountId = $('#payAccountForm').find('input[name="account_id"]').val();
            var branchId = $('#payAccountForm').find('input[name="branch_id"]').val();
            var date = $('#input_date').val();
            // Check if any required fields are missing
            if (!amount) {
                alert('Please enter an amount.');
                return;
            }
            progressBTN($('#btnpayAccount'), true);
            var formData = {
                amount: amount,
                account_id: accountId,
                branch_id: branchId,
                date: date
            };

            $.ajax({
                url: "{{ route('accounts.pay-cash') }}",
                type: 'POST',
                data: formData,
                success: function(response) {


                    if (response.status == true) {
                        $("#pay_amount").val(0)
                        tbl.ajax.reload();
                        displaySuccessMessage(response.message);
                    } else {
                        displayErrorMessage(response.message);
                    }
                    progressBTN($('#btnpayAccount'), false);
                    // You can also redirect or update the page as needed
                    $('#payAccountModal').modal('hide');
                },
                error: function(error) {
                    progressBTN($('#btnpayAccount'), false);
                    alert('Error: ' + error.responseJSON.message);
                    console.log(error);
                }
            });
        });


        $('#payAccountFormUpdate').on('submit', function(e) {
            e.preventDefault();

            var amount = $('#update_pay_amount').val();
            var accountId = $('#payAccountFormUpdate').find('input[name="account_id"]').val();
            var branchId = $('#payAccountFormUpdate').find('input[name="branch_id"]').val();
            var date = $('#update_input_date').val();
            // Check if any required fields are missing
            if (!amount) {
                alert('Please enter an amount.');
                return;
            }
            progressBTN($('#btnupdatePayAccount'), true);
            var formData = {
                amount: amount,
                account_id: accountId,
                branch_id: branchId,
                date: date
            };

            $.ajax({
                url: "{{ route('accounts.update-cash') }}",
                type: 'POST',
                data: formData,
                success: function(response) {


                    if (response.status == true) {
                        $("#update_pay_amount").val(0)
                        tbl.ajax.reload();
                        displaySuccessMessage(response.message);
                    } else {
                        displayErrorMessage(response.message);
                    }
                    progressBTN($('#btnupdatePayAccount'), false);
                    // You can also redirect or update the page as needed
                    $('#updatePayAccountModal').modal('hide');
                },
                error: function(error) {
                    progressBTN($('#btnupdatePayAccount'), false);
                    alert('Error: ' + error.responseJSON.message);
                    console.log(error);
                }
            });
        });
    </script>
@endsection
