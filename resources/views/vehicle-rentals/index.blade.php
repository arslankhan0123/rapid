@extends('layouts.app')
@section('title')
    {{ __('messages.vehicle-rentals.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.vehicle-rentals.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center mb-3">
                <!-- Month Filters -->
                <div class="d-flex">
                    <div class="me-2">
                        <label for="startMonth"> Month</label>
                        <select id="startMonth" class="form-control">
                            <option value="">Select Month</option>
                        </select>
                    </div>
                    <div class="me-2 ml-2 d-none">
                        <label for="endMonth">End Month</label>
                        <select id="endMonth" class="form-control">
                            <option value="">Select Month</option>
                        </select>
                    </div>
                    <div class="me-2 ml-2">
                        <label for="endMonth">Year</label>
                        <select id="yearSelect" name="yearSelect" class="form-control">
                            <option value='0'>Select year</option>
                            <!-- Years will be added dynamically -->
                        </select>
                    </div>
                    <div class="me-2 ml-2">
                        <label for="endMonth">Type</label>
                        {{ Form::select('agreement_type', ['One-time' => 'One-time', 'Installment' => 'Installment'], null, [
                            'class' => 'form-control',
                            'id' => 'agreement_type',
                            'placeholder' => __('Select Type'),
                        ]) }}
                    </div>


                </div>

                <!-- Add Button -->
                @can('create_vehicle_rental')
                    <a href="{{ route('vehicle-rentals.create') }}" class="btn btn-primary ms-2 mt-4 ml-2"
                        style="line-height: 30px;">{{ __('messages.vehicle-rentals.add') }}</a>
                @endcan
            </div>

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
                    @include('vehicle-rentals.table_unit')
                </div>
            </div>
        </div>

    </section>
    @include('vehicle-rentals.pay_modal')
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
        const yearSelect = document.getElementById("yearSelect");
        const currentYear = new Date().getFullYear();
        const startYear = 1900;

        for (let year = currentYear; year >= startYear; year--) {
            let option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }

        let tbl = $('#assetTable').DataTable({
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
            order: [
                [0, 'desc']
            ], // Ordering by the 3rd column (index starts from 0)

            ajax: {
                url: route('vehicle-rentals.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                    d.startMonth = $("#startMonth").val();
                    d.endMonth = $("#endMonth").val();
                    d.year = $("#yearSelect").val();
                    d.agreement_type = $("#agreement_type").val();

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
                        return row.rental_number ?? '';
                    },
                    name: 'rental_number',
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return row.plate_number ?? '';
                    },
                    name: 'plate_number',
                    className: "text-center" // ✅ centered now
                },
                {
                    data: function(row) {
                        return row.name ?? '';
                    },
                    name: 'name',
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return row.type ?? '';
                    },
                    name: 'type',
                    orderable: false,
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return row.agreement_type ?? '';
                    },
                    name: 'agreement_type',
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return row.agreement_date ? moment(row.agreement_date).format('DD-MM-YYYY') : '';
                    },
                    name: 'agreement_date',
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return row.expiry_date ? moment(row.expiry_date).format('DD-MM-YYYY') : '';
                    },
                    name: 'expiry_date',
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return row.amount ?? 0;
                    },
                    name: 'amount',
                    className: "text-right"
                },
                {
                    data: function(row) {
                        return (row.amount - row.paid_amount) ?? 0;
                    },
                    name: 'monthly_amount',
                    className: "text-right"
                },
                {
                    data: function(row) {
                        return row.paid_amount ?? 0;
                    },
                    name: 'paid_amount',
                    className: "text-right"
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id, row);
                    },
                    name: 'id',
                    className: "text-center"
                }
            ],


            responsive: true, // Enable responsive features

            dom: "Bfrtip", // Ensure Buttons (B) is part of the DOM structure
            buttons: [{
                    extend: 'csvHtml5',
                    text: '<i class="fas fa-file-csv"></i> Export CSV',
                    className: 'btn btn-sm',
                    title: ' Rentals Report', // Set the custom file name here
                    exportOptions: {
                        // Exclude the action column from the export
                        columns: function(idx, data, node) {
                            return idx !== 10; // Exclude the action column at index 4
                        }
                    }
                }, {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    className: 'btn btn-sm',
                    title: ' Rentals Report',
                    exportOptions: {
                        // Exclude the action column from the export
                        columns: function(idx, data, node) {
                            return idx !== 10; // Exclude the action column at index 4
                        }
                    }
                }, {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                    className: 'btn btn-sm', // Styled button
                    orientation: 'portrait',
                    title: ' Rentals Report',
                    pageSize: 'A3',
                    exportOptions: {
                        // Exclude the action column from the export
                        columns: function(idx, data, node) {
                            return idx !== 10; // Exclude the action column at index 7
                        }
                    },
                    customize: function(doc) {
                        // Set a little margin (5 units)
                        doc.pageMargins = [5, 5, 5, 5]; // Top, Right, Bottom, Left

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


            ],
        });
        $('#startMonth, #endMonth,#yearSelect,#agreement_type').change(function() {
            tbl.ajax.reload();
        });

        $(document).on('click', '.edit-btn', function(event) {
            let assetId = $(event.currentTarget).data('id');
            const url = route('vehicle-rentals.edit', assetId);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('vehicle-rentals.destroy', assetCateogryId), '#assetTable',
                "{{ __('messages.vehicle-rentals.name') }}");
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
            updateItem: "{{ auth()->user()->can('update_vehicle_rental') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_vehicle_rental') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_vehicle_rental') ? 'true' : 'false' }}",
            payItem: "{{ auth()->user()->can('pay_vehicle_rental') ? 'true' : 'false' }}",
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id, row = null) {


            let buttons = '';
            // if (permissions.payItem === 'true') {
            //     buttons += `
        //         <a title="Pay" href="#" class="btn btn-success action-btn has-icon pay-btn"
        //         data-id="${id}" style="float:right;margin:2px;width:50px;"
        //         onclick='openPayModal(${JSON.stringify(row)})'>
        //             Pay
        //         </a>
        //         `;
            // }
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('vehicle-rentals.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('vehicle-rentals.view', ':id') }}`;
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

        // Function to open payment modal
        function openPayModal(row) {
            $('#payModal').modal('show');

            // Ensure row is properly parsed if passed as a JSON string
            if (typeof row === "string") {
                row = JSON.parse(row);
            }

            $('#payModal input[name="rental_id"]').val(row.id);
            $('#payModal select[name="branch_id"]').val(row.branch_id).trigger('change');
            $('#payModal select[name="account_id"]').val(row.account_id).trigger('change');
            $('#payModal input[name="paid_amount"]').val(row.paid_amount);
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let months = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            let startMonthSelect = document.getElementById("startMonth");
            let endMonthSelect = document.getElementById("endMonth");

            // Populate both dropdowns with months
            months.forEach((month, index) => {
                let option = `<option value="${index + 1}">${month}</option>`;
                startMonthSelect.innerHTML += option;
                endMonthSelect.innerHTML += option;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Store account data in a JavaScript object
            var accounts = @json($accounts);

            $('#branch').change(function() {
                var branchId = $(this).val(); // Get selected branch ID
                var $accountSelect = $('#selectAcount');

                // Clear previous options
                $accountSelect.empty();
                $accountSelect.append('<option value="">Select Cash Account</option>');

                // Filter accounts based on the selected branch
                $.each(accounts, function(index, account) {
                    if (account.branch_id == branchId) {
                        $accountSelect.append('<option value="' + account.id + '">' + account
                            .account_name + '</option>');
                    }
                });
            });


            $('#payForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                startLoader();
                var formData = $(this).serialize(); // Serialize form data
                var rentalId = $('input[name="rental_id"]').val(); // Get rental_id

                $.ajax({
                    url: route('vehicle-rentals.update-payment', rentalId), // Dynamic route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#payModal').modal('hide');
                        displaySuccessMessage(response.message);
                        tbl.ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
