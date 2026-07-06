@extends('layouts.app')
@section('title')
    {{ __('messages.account-statements.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.account-statements.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    @can('export_statement')
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                                style="line-height: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Export
                            </button>
                            <div class="dropdown-menu " aria-labelledby="exportDropdown" style="width: 50px;">
                                <a class="dropdown-item" href="#" id="export-pdf">PDF</a>
                                <a class="dropdown-item" href="#" id="export-csv">CSV</a>
                            </div>
                        </div>
                    @endcan

                </div>
            </div>
            <div class="float-right">

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('from_date', __('messages.account-statements.from')) }}
                            <input type="date" id="from_date" name="from_date" class="form-control">
                        </div>

                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('to_date', __('messages.account-statements.to')) }}
                            <input type="date" id="to_date" name="to_date" class="form-control">
                        </div>
                        <div class="form-group col-12 col-md-3">
                            {{ Form::label('department', __('messages.branches.name') . ':') }}
                            {{ Form::select('department_id', $usersBranches, null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect']) }}
                        </div>


                        <div class="form-group col-12 col-md-3">
                            {{ Form::label('employee_id', __('messages.accounts.name') . ':') }}
                            {{ Form::select('employee_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'accountSelect']) }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    @include('account-statements.table')
                </div>
            </div>
        </div>
    </section>
    {{-- @include('salary_generates.templates.templates') --}}
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        var tmpBalance = 0;
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
                url: route('account-statements.index'),
                data: function(d) {

                    // Attach the selected month, customer, and project to the AJAX request
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.branch_id = $('#branchSelect').val();
                    d.account_id = $('#accountSelect').val();

                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },

            columnDefs: [],
            columns: [{
                    data: function(row) {
                        return row[0] ?? '';
                    },
                    className: 'text-left',
                },
                {
                    data: function(row, type, set, meta) {
                        return meta.row + 1; // Row number (starting from 1)
                    },
                    className: 'text-center',
                },
                {
                    data: function(row) {
                        return row[1] ?? '';
                    },
                    name: '',
                    orderable: false, // Disable sorting
                    searchable: false,
                    className: 'text-start',
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row[2] ?? '';
                        return element.value;
                    },
                    name: '',
                    className: 'text-left',
                },
                {
                    data: function(row) {
                        return Number(row[3] ?? 0).toFixed(
                            2); // Credit value (formatted to 2 decimal places)
                    },
                    name: '',
                    className: 'text-right',
                },
                {
                    data: function(row) {
                        return Number(row[4] ?? 0).toFixed(
                            2); // Debit value (formatted to 2 decimal places)
                    },
                    name: '',
                    className: 'text-right',
                },
                {
                    data: function(row) {

                        // Calculate the difference (debit - credit) if needed
                        return Number(row[5] ?? 0).toFixed(2);
                    },
                    name: '',
                    className: 'text-right',
                }

            ],


            responsive: true,

            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            // Callback for row creation

            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Sum up values for debit, credit, and balance columns
                var totalDebit = api.column(4).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                var totalCredit = api.column(5).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);



                // Update the footer
                $(api.column(4).footer()).html(totalDebit.toFixed(2));
                $(api.column(5).footer()).html(totalCredit.toFixed(2));
                $(api.column(6).footer()).html(Math.abs((totalDebit - totalCredit).toFixed(2)));
            },



        });

        $('#branchSelect,#accountSelect').on('change', function() {
            tbl.ajax.reload(); // Reload the DataTable
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#export-pdf').on('click', function(e) {
                e.preventDefault();

                // Get the values from form inputs
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var branch_id = $('#branchSelect').val();
                var account_id = $('#accountSelect').val();

                // Construct the URL for PDF export dynamically
                var exportUrl = "{{ route('account-statements.export') }}?type=pdf";
                var queryParams = [];

                if (fromDate) queryParams.push("from_date=" + encodeURIComponent(fromDate));
                if (toDate) queryParams.push("to_date=" + encodeURIComponent(toDate));
                if (branch_id) queryParams.push("branch_id=" + encodeURIComponent(branch_id));
                if (account_id) queryParams.push("account_id=" + encodeURIComponent(account_id));

                if (queryParams.length > 0) {
                    exportUrl += "&" + queryParams.join("&");
                }

                // Redirect to the constructed URL
                window.location.href = exportUrl;
            });

            // Handle CSV export
            $('#export-csv').on('click', function(e) {
                e.preventDefault();

                // Get the values from form inputs
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var branch_id = $('#branchSelect').val();
                var account_id = $('#accountSelect').val();

                // Construct the URL for CSV export dynamically
                var exportUrl = "{{ route('account-statements.export') }}?type=csv";
                var queryParams = [];

                if (fromDate) queryParams.push("from_date=" + encodeURIComponent(fromDate));
                if (toDate) queryParams.push("to_date=" + encodeURIComponent(toDate));
                if (branch_id) queryParams.push("branch_id=" + encodeURIComponent(branch_id));
                if (account_id) queryParams.push("account_id=" + encodeURIComponent(account_id));

                if (queryParams.length > 0) {
                    exportUrl += "&" + queryParams.join("&");
                }

                // Redirect to the constructed URL
                window.location.href = exportUrl;
            });


        });
    </script>

    <script>
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

            // Add all accounts for default display
            accountsByBranch['all'] = accountsData.map(account => ({
                id: account.id,
                name: account.account_name,
            }));

            // Handle branch selection change
            $('#branchSelect').change(function() {

                const branchId = $(this).val();
                const filteredAccounts = branchId ? accountsByBranch[branchId] || [] : accountsByBranch[
                    'all'];

                // Update options for account dropdown
                updateDropdown('#accountSelect', filteredAccounts);
            });

            const defaultBranchId = $('#branchSelect').val(); // Get the default selected branch
            const filteredAccounts = defaultBranchId ? accountsByBranch[defaultBranchId] || [] : accountsByBranch[
                'all'];

            console.log(defaultBranchId, filteredAccounts);
            updateDropdown('#accountSelect', filteredAccounts);

            function updateDropdown(selector, accounts) {


                const dropdown = $(selector);
                dropdown.empty();
                // dropdown.append('<option value="" disabled selected>Select Account</option>');
                accounts.forEach(account => {
                    dropdown.append(`<option value="${account.id}">${account.name}</option>`);
                });

                tbl.ajax.reload();
            }

            // Initialize with all accounts (if no branch is pre-selected)
            //  updateDropdown('#accountSelect', accountsByBranch['all']);
        });
    </script>
@endsection
