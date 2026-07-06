@extends('layouts.app')
@section('title')
    {{ __('messages.opening-stocks.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.opening-stocks.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{-- {{Form::select('status', $statusArr, null, ['id' => 'filterStatus', 'class' => 'form-control','placeholder' =>__('messages.placeholder.select_status')]) }} --}}
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('opening-stocks.create') }}"
                    class="btn btn-primary form-btn">{{ __('messages.common.add') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @include('opening-stocks.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
@endsection
@section('scripts')
    <script>
        let customerId = null;
    </script>
    <script src="{{ mix('assets/js/estimates/estimates-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.colVis.min.js') }}"></script>

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
                url: route('opening-stocks.index'),
            },
            dom: "B" + // Buttons
                "rt",

            buttons: [

                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>{{ __('messages.pos_label.export_excel') }}',
                    className: 'btn btn-sm',
                    exportOptions: {
                        // Exclude the action column from the export
                        columns: function(idx, data, node) {
                            return idx !== 5;
                        }
                    }
                }


            ],
            order: [
                [0, 'desc'] // Ordering by the hidden 'created_at' column (index 2) in descending order
            ],
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.estimate_number ?? '';
                        return element.value;
                    },
                    name: 'estimate_number',
                    width: '10%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        // Convert created_at to Date object and format it as d-m-Y
                        let date = new Date(row.created_at);
                        if (!isNaN(date)) {
                            // Format the date as day-month-year (d-m-Y)
                            let day = String(date.getDate()).padStart(2, '0');
                            let month = String(date.getMonth() + 1).padStart(2,
                                '0'); // Months are zero-based
                            let year = date.getFullYear();
                            return `${day}-${month}-${year}`;
                        } else {
                            return ''; // Handle case where created_at is null or invalid
                        }
                    },
                    name: 'created_at',
                    width: '15%',
                    className: 'text-center'
                },


                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.client_note ?? '';
                        return element.value;
                    },
                    name:'client_note',
                    width: '5%',
                    className: 'text-center'
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.total_amount;
                        return element.value;
                    },
                    name: 'total_amount',
                    width: '10%',
                    className: 'text-right'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '10%'
                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('opening-stocks.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('opening-stocks.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.opening-stocks.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_opening_stocks') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_opening_stocks') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_opening_stocks') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('opening-stocks.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('opening-stocks.view', ':id') }}`;
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
@endsection
