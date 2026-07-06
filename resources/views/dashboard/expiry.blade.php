@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .edit-icon {
            cursor: pointer;
            margin-left: 5px;
            color: #007bff;
            /* Change to your desired color */
        }

        .date-value {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <section class="section">
        <div class="section-header">

            <h1>{{ __('messages.employees.id_expire') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('dashboard') }}"
                    class="btn btn-primary form-btn">{{ __('messages.dashboard') }}</a>
            </div>
        </div>
        @include('flash::message')

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-dark w-100"></h6>

                        <div>
                            {!! Form::select('idType', ['iqama_no' => 'Iqama No', 'tuv_no' => 'TUV', 'passport' => 'Passport'], null, [
                                'class' => 'form-control',
                                'id' => 'idTypeSelect',
                                'style' => 'border-radius:0px;width:200px;',
                            ]) !!}
                        </div>
                        <div style="width: 50px;"> </div>
                        <div>
                            {!! Form::select('month', $months, $currentMonth, ['class' => 'form-control', 'id' => 'monthId']) !!}
                        </div>

                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-responsive-sm" id="employeeIDExpireTable">
                            <thead class="text-white contract-table-bg-color">
                                <tr>
                                    <td id="idType">{{ __('messages.employees.id') }}</td>
                                    <td>{{ __('messages.employees.name') }}</td>
                                    <td>{{ __('messages.designations.name') }}</td>
                                    <td>{{ __('messages.employees.expire_date') }}</td>
                                </tr>
                            </thead>
                            <tbody class="expiring-contracts">
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/chart/Chart.min.js') }}"></script>
    <script src="{{ mix('assets/js/dashboard/dashboard.js') }}"></script>


    <script>
        function initializeDataTable(idType) {
            // Determine the header and data field based on the selected ID type
            let idTypeHeader = '';
            let dateField = '';
            let idField = '';

            if (idType === 'iqama_no') {
                idTypeHeader = '{{ __('messages.employees.iqama_no') }}';
                idField = 'iqama_no';
                dateField = 'iqama_no_expiry_date';
            } else if (idType === 'tuv_no') {
                idTypeHeader = '{{ __('messages.employees.tuv_no') }}';
                idField = 'tuv_no';
                dateField = 'tuv_no_expiry_date';
            } else if (idType === 'passport') {
                idTypeHeader = '{{ __('messages.employees.passport') }}';
                idField = 'passport';
                dateField = 'passport_expiry_date';
            }


            // Destroy the existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#employeeIDExpireTable')) {
                $('#employeeIDExpireTable').DataTable().destroy();
            }

            // Update the table header dynamically
            $('#employeeIDExpireTable thead').html(`
                <tr>
                    <th style='color:white;'>${idTypeHeader}</th>
                    <th style='color:white;'>{{ __('messages.employees.name') }}</th>
                    <th style='color:white;'>{{ __('messages.designations.name') }}</th>
                    <th style='color:white;'>{{ __('messages.employees.expire_date') }}</th>
                </tr>
           `);

            // Define the columns based on the selected ID type
            let columns = [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row[idField] ?? '';
                        return element.value;
                    },
                    name: idField,
                    width: '15%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name ?? '';
                        return element.value;
                    },
                    name: 'name',
                    width: '15%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.designation.name ?? '';
                        return element.value;
                    },
                    name: 'designation.name',
                    width: '15%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let dateStr = row[dateField] ?? '';
                        if (dateStr) {
                            let date = new Date(dateStr);
                            let day = ("0" + date.getDate()).slice(-2);
                            let month = ("0" + (date.getMonth() + 1)).slice(-2);
                            let year = date.getFullYear();
                            // Add an edit icon beside the date if user has permission
                            return `
                                <span class="date-value">
                                    ${day}-${month}-${year}
                                </span>
                                @can('update_employees')
                                <i class="edit-icon" data-id="${row.id}">&#9998;</i>
                                @endcan
                            `;
                        } else {
                            return '';
                        }
                    },
                    name: dateField,
                    width: '15%',
                    orderable: false
                }
            ];

            // Reinitialize the DataTable
            $('#employeeIDExpireTable').DataTable({
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
                    url: route('dashboard.employees'),
                    data: function(d) {
                        d.month = $('#monthId').val(); // Get the selected month
                        d.idType = $('#idTypeSelect').val(); // Get the selected ID type
                    }
                },
                columns: columns, // Apply the dynamic columns
                responsive: true,

                // Disable pagination, info, and length menu

            });
        }



        // Initialize DataTable on page load with default ID type
        initializeDataTable($('#idTypeSelect').val());

        // Add event listeners to the month and ID type dropdowns
        $('#monthId').change(function() {
            $('#employeeIDExpireTable').DataTable().ajax.reload(); // Reload DataTable when month changes
        });

        // Handle change of ID type and reinitialize the DataTable
        $('#idTypeSelect').change(function() {
            let selectedIdType = $(this).val();
            initializeDataTable(selectedIdType); // Reinitialize the DataTable with new ID type
        });

        // Handle click events on the edit icon
        $('#employeeIDExpireTable').on('click', '.edit-icon', function() {
            let employeeId = $(this).data('id');
            // Redirect only if user has permission
            @can('update_employees')
                window.location.href = route('employees.edit', {
                    employee: employeeId
                });
            @else
                alert('You do not have permission to edit employees.');
            @endcan
        });
    </script>
@endsection
