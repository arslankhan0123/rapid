@extends('layouts.app')
@section('title')
    Manual Sales
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ mix('assets/css/invoices/invoices.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Add month picker CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css">
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Manual Invoice</h1>
            <div class="section-header-breadcrumb float-right">
                {{-- Month Picker --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        {{ Form::text('month', null, [
                            'id' => 'filterMonth',
                            'class' => 'form-control month-picker',
                            'placeholder' => __('messages.placeholder.select_month'),
                            'autocomplete' => 'off',
                        ]) }}
                        <input type="hidden" id="filterMonthValue">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-month" title="Clear Month Filter">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Customer Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="input-group">
                        {{ Form::select('customer', $customers ?? [], null, [
                            'id' => 'filterCustomer',
                            'class' => 'form-control select2-sm',
                            'placeholder' => __('messages.placeholder.select_customer'),
                            'style' => 'min-width: 180px;',
                        ]) }}
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary clear-customer"
                                title="Clear Customer Filter">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Branch Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('branches', $usersBranches ?? [], null, [
                        'id' => 'filterBranch',
                        'class' => 'form-control select2-sm',
                        'placeholder' => __('messages.placeholder.branches'),
                        'style' => 'min-width: 150px;',
                    ]) }}
                </div>

                {{-- Payment Status Filter --}}
                <div class="card-header-action mr-3 select2-mobile-margin d-none">
                    {{ Form::select('payment_status', $paymentStatuses, null, [
                        'id' => 'paymentStatus',
                        'class' => 'form-control select2-sm',
                        'placeholder' => __('messages.placeholder.select_status'),
                        'style' => 'min-width: 150px;',
                    ]) }}
                </div>
            </div>
            <div class="float-right">
                @can('create_manual_sales')
                    <a href="{{ route('manual-sales.create') }}"
                        class="btn btn-primary form-btn">{{ __('messages.common.add') }}
                    </a>
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
                    @include('manual-sales.table')
                </div>
            </div>
        </div>
    </section>
    @include('manual-sales.templates.templates')
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('scripts')
    <script>
        let invoiceUrl = "{{ route('manual-sales.index') }}";
        let customerId = null;
    </script>

    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#filterMonth').attr('autocomplete', 'off').blur();

            // Prevent opening on focus
            $('#filterMonth').on('focus', function(e) {
                e.preventDefault();
                $(this).blur();
            });

            // Initialize month picker (show Month name like "October 2025")
            $('#filterMonth').datepicker({
                format: "MM yyyy",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true,
                endDate: new Date(),
                templates: {
                    leftArrow: '<i class="fas fa-chevron-left"></i>',
                    rightArrow: '<i class="fas fa-chevron-right"></i>'
                }
            }).on('changeDate', function(e) {
                if (e.date) {
                    const date = e.date;
                    const year = date.getFullYear();
                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                    $('#filterMonthValue').val(`${year}-${month}`); // send to backend

                    const monthNames = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    $('#filterMonth').val(`${monthNames[date.getMonth()]} ${year}`); // display
                    tbl.ajax.reload();
                }
            });

            // Clear month filter
            $('.clear-month').on('click', function() {
                $('#filterMonth').val('');
                $('#filterMonthValue').val('');
                tbl.ajax.reload();
            });

            // Initialize customer select2
            $('#filterCustomer').select2({
                width: '180px',
                dropdownAutoWidth: true,
                placeholder: "Select Customer",
                allowClear: true
            });

            // Clear customer selection
            $(document).on('click', '.clear-customer', function() {
                $('#filterCustomer').val(null).trigger('change');
                tbl.ajax.reload();
            });

            // Initialize branch select2
            $('#filterBranch').select2({
                width: '150px',
                dropdownAutoWidth: true,
                placeholder: "Select Branch",
                allowClear: true
            });

            // Initialize payment status select2
            $('#paymentStatus').select2({
                width: '150px',
                dropdownAutoWidth: true,
                placeholder: "Select Status",
                allowClear: true
            });

            // Reload DataTable on filter change
            $('#filterCustomer, #filterBranch, #paymentStatus').on('change', function() {
                tbl.ajax.reload();
            });
        });
    </script>

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
                url: route('manual-sales.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                    d.filterMonth = $("#filterMonthValue").val(); // send YYYY-MM to backend
                    d.filterCustomer = $("#filterCustomer").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100,
            columns: [{
                    data: row => row.invoice_number ?? '',
                    name: 'invoice_number',
                    width: '150px',
                    className: 'text-center'
                },
                {
                    data: row => row.branch?.name ?? '',
                    name: 'branch.name'
                },
                {
                    data: row => row.customer?.company_name ?? row.customer_name,
                    name: 'customer.company_name'
                },
                {
                    data: row => row.project?.project_name ?? row.project?.project_name ?? '-',
                    name: 'project.name',
                    width: '150px'
                },
                {
                    data: row => row.invoice_date_formatted ?? '',
                    name: 'invoice_date'
                },
                {
                    data: function(row) {
                        let paymentStatus = '';
                        if (row.payment_status == 0)
                            return `<span class="badge bg-danger text-white">Drafted</span>`;
                        const paymentLabel = {
                            1: 'Unpaid',
                            2: 'Paid',
                            3: 'Partially Paid',
                            4: 'Cancelled'
                        };
                        paymentStatus =
                            `<span class="badge bg-primary text-white d-none">${paymentLabel[row.payment_status]??'Unknown'}</span>`;
                        let approvalStatus = '';
                        if (row.status == 0) approvalStatus =
                            `<span class="badge bg-warning text-white">Pending</span>`;
                        else if (row.status == 1) approvalStatus =
                            `<span class="badge bg-success text-white">Approved</span>`;
                        return `${paymentStatus} ${approvalStatus}`;
                    },
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    width: '80px',
                    className: 'text-right'
                },
                {
                    data: row => `<p class="text-right">${parseFloat(row.total_amount).toFixed(2)}</p>`,
                    name: 'total_amount'
                },
                {
                    data: row => renderActionButtons(row.id),
                    name: 'id'
                }
            ],
            responsive: true
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            window.location.href = route('manual-sales.edit', did);
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('manual-sales.destroy', assetCateogryId), '#designationTable', 'Sales Invoice');
        });

        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            edit: "{{ __('messages.common.edit') }}",
            view: "{{ __('messages.common.view') }}"
        };

        var permissions = {
            updateItem: "{{ auth()->user()->can('update_manual_sales') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_manual_sales') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_manual_sales') ? 'true' : 'false' }}"
        };

        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('manual-sales.edit', ':id') }}`.replace(':id', id);
                buttons +=
                    `<a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;"><i class="fa fa-edit"></i></a>`;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('manual-sales.show', ':id') }}`.replace(':id', id);
                buttons +=
                    `<a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;"><i class="fa fa-eye"></i></a>`;
            }
            if (permissions.deleteItem === 'true') {
                buttons +=
                    `<a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;"><i class="fa fa-trash"></i></a>`;
            }
            return buttons;
        }
    </script>
@endsection
