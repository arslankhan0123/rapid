@extends('layouts.app')
@section('title')
    {{ __('messages.backup.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <!-- Custom CSS -->
    <style>
        /* Background and text color for the active tab */
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            /* Active background color */
            color: #fff !important;
            /* Active text color set to white */
        }

        /* Text color for non-active tabs */
        .nav-tabs .nav-link {
            color: #007bff;
            /* Default text color */
        }

        /* Hover effect for non-active tabs */
        .nav-tabs .nav-link:hover {
            color: #0056b3;
            /* Hover text color */
        }

        #importBtn {
            line-height: 30px !important;

        }


        .btn-primary {
            /* background: red !important; */
            border: none;
            height: 40px;
            line-height: 30px !important;
            min-width: 100px;
            font-size: 18px !important;
        }

        .btn-primary i {
            font-size: 20px !important;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.backup.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                @can('create_backup')
                    <div class="card-header-action mr-3 select2-mobile-margin" id="createDiv">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                            Create Backup
                        </button>
                    </div>
                @endcan

                @can('restore')
                    <div class="section-header-breadcrumb float-right" id="restoreDiv">
                        <div class="card-header-action mr-3 select2-mobile-margin">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#importModalRestore">
                                Import
                            </button>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        @if (session()->has('flash_notification'))
            @foreach (session('flash_notification') as $message)
                <div class="alert alert-{{ $message['level'] }}">
                    {{ $message['message'] }}
                </div>
            @endforeach
        @endif

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="backupRestoreTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="backup-tab" data-toggle="tab" href="#backup" role="tab"
                                aria-controls="backup" aria-selected="true">Backup</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="restore-tab" data-toggle="tab" href="#restore" role="tab"
                                aria-controls="restore" aria-selected="false">Restore</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="backupRestoreTabContent">
                        <div class="tab-pane fade show active" id="backup" role="tabpanel" aria-labelledby="backup-tab">
                            <!-- Backup Content -->
                            @include('db_backup.datatable')
                        </div>
                        <div class="tab-pane fade" id="restore" role="tabpanel" aria-labelledby="restore-tab">
                            <!-- Restore Content -->
                            @include('db_backup.restore')
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
    @include('db_backup.backup_modal')
    @include('db_backup.delete_modal')
    @include('db_backup.restore_modal')


    @include('restore.backup_modal')
    @include('restore.delete_modal')
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
        $(document).ready(function() {
            // Hide and disable restoreDiv initially since Backup is active by default
            $('#restoreDiv').hide().prop('disabled', true);

            // When the Backup tab is clicked
            $('#backup-tab').on('click', function() {
                $('#createDiv').show().prop('disabled', false); // Show and enable createDiv
                $('#restoreDiv').hide().prop('disabled', true); // Hide and disable restoreDiv
            });

            // When the Restore tab is clicked
            $('#restore-tab').on('click', function() {
                $('#restoreDiv').show().prop('disabled', false); // Show and enable restoreDiv
                $('#createDiv').hide().prop('disabled', true); // Hide and disable createDiv
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#backupTable').DataTable();
            var restoreTable = $('#restoreDataTables').DataTable();
            // Handle create backup button click using AJAX
            $('#createBackupBtn').on('click', function() {
                // Show loading state
                var $button = $(this);
                var $btnText = $button.find('.btn-text');
                var $spinner = $button.find('.spinner-border');

                $btnText.text('Creating Backup...');
                $spinner.removeClass('d-none');

                $.ajax({
                    url: '{{ route('backup.create') }}', // Route for creating backup
                    type: 'GET',
                    success: function(response) {
                        // Redirect to backup index after successful creation
                        const url = '{{ route('backup.index') }}';
                        window.location.href = url;
                    },
                    error: function(xhr) {
                        // Show error message
                        $('body').prepend(
                            '<div class="alert alert-danger">Failed to create backup.</div>'
                        );
                    },
                    complete: function() {
                        // Revert button to normal state
                        $btnText.text('Create Backup');
                        $spinner.addClass('d-none');
                    }
                });
            });


            // Handle delete button click
            $('.delete-backup').on('click', function() {
                var backupId = $(this).data('id'); // Get the backup ID from the data attribute
                console.log(backupId); // Log the ID for debugging
                $('#confirmDelete').data('id', backupId); // Pass the backup ID to the modal
                $('#deleteModal').modal('show'); // Show the delete confirmation modal
            });

            // Confirm delete
            $('#confirmDelete').on('click', function() {
                var backupId = $(this).data('id'); // Get the backup ID from the modal's data attribute

                // Use the correct route and dynamically replace the ID in the URL
                $.ajax({
                    url: '{{ route('backup.delete', ':id') }}'.replace(':id',
                        backupId), // Dynamically replace ":id" with backupId
                    type: 'DELETE',
                    success: function(response) {
                        // Redirect or reload the page after successful deletion
                        window.location
                            .reload(); // Reload the page to refresh the list of backups
                    },
                    error: function(xhr) {
                        // Handle any errors (optional)
                        alert('Failed to delete the backup.');
                    }
                });
                $('#deleteModal').modal('hide'); // Hide the modal after delete is confirmed
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            // Handle the manual option click
            $('#manualOption').click(function() {
                $('#manualSection').show(); // Show manual section
                $('#automaticSection').hide(); // Hide automatic section
            });

            // Handle the automatic option click
            $('#automaticOption').click(function() {
                $('#manualSection').hide(); // Hide manual section
                $('#automaticSection').show(); // Show automatic section
            });

            $("#automaticOption").trigger('click');
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
            }
        }
    </script>

    <script>
        function showRestoreModal(fileName, id = null) {

            // Show the modal
            $('#restoreModal').modal('show');
            // Handle the confirm button click
            $('#confirmRestore').off('click').on('click', function() {
                // Close the modal
                processingBtn("#confirmRestore", 1);
                // Make the AJAX call to restore the backup
                $.ajax({
                    url: '{{ route('restore.from-file') }}', // Route for creating backup
                    method: 'post',
                    data: {
                        file: fileName,
                        id: id,
                        _token: '{{ csrf_token() }}' // Ensure you include the CSRF token
                    },
                    success: function(response) {
                        $('#restoreModal').modal('hide');
                        processingBtn("#confirmRestore", 0);
                        displaySuccessMessage(response.message);
                        const url = '{{ route('backup.index') }}';
                        window.location.href = url;
                    },
                    error: function(xhr, status, error) {
                        // Handle the error response
                        $('#restoreModal').modal('hide');
                        processingBtn("#confirmRestore", 0);
                        displayErrorMessage(error);
                    }
                });
            });
        }
    </script>


    {{-- below restore --}}
    <script>
        let baseDownloadUrl = `{{ asset('uploads/restores') }}/`;
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
                url: route('restore.index'),
            },
            columns: [{
                    data: function(row) {
                        // Use the base path and append the file name dynamically
                        let downloadUrl = baseDownloadUrl + row.file_name;

                        // Return the HTML for the download link with an icon
                        return `
                        <a href="${downloadUrl}" class="btn text-primary" download>
                            ${row.file_name} <i class="fa fa-download" aria-hidden="true" style="padding-left:5px;"></i>
                        </a>
                    `;
                    },
                    name: 'file_name',
                    width: '20%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.user ? row.user.first_name + " " + row.user.last_name : '';
                    },
                    name: 'file_name',
                    width: '20%'
                }, {
                    data: function(row) {
                        // Create a new Date object from the row's date string
                        let date = new Date(row.created_at);

                        // Format the date to d-m-Y and time to 12-hour format
                        let day = ('0' + date.getDate()).slice(-2);
                        let month = ('0' + (date.getMonth() + 1)).slice(-2);
                        let year = date.getFullYear();
                        let hours = date.getHours() % 12 || 12; // Convert to 12-hour format
                        let minutes = ('0' + date.getMinutes()).slice(-2);
                        let ampm = date.getHours() >= 12 ? 'PM' : 'AM';

                        // Construct the formatted date string
                        let formattedDate = `${day}-${month}-${year}`;
                        let formattedTime = `${hours}:${minutes} ${ampm}`;

                        return `${formattedDate} ${formattedTime}`;
                    },
                    name: 'created_at',
                    width: '15%'
                }

            ],
            responsive: true // Enable responsive features
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#restoreForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission
                processingBtn('#submitBtn', 1);
                var formData = new FormData(this); // Create a FormData object

                // Show the progress container
                $('#progressContainer').show();
                $('#uploadProgressBar').css('width', '0%').text('0%');

                $.ajax({
                    url: $(this).attr('action'), // Form action URL
                    type: 'POST',
                    data: formData,
                    processData: false, // Prevent jQuery from automatically transforming the data into a query string
                    contentType: false, // Prevent jQuery from setting content-type header
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        // Upload progress
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt
                                    .total) * 100);

                                if (percentComplete === 100) {
                                    // Show "Processing..." when upload reaches 100%
                                    $('#uploadProgressBar').css('width', '100%').text(
                                        'Processing...');
                                } else {
                                    // Update progress bar with percentage
                                    $('#uploadProgressBar').css('width',
                                        percentComplete + '%').text(
                                        percentComplete + '%');
                                }
                            }
                        }, false);

                        return xhr;
                    },
                    success: function(response) {
                        // Handle success response
                        tbl.ajax.reload(null, false);
                        displaySuccessMessage(response.message);
                        processingBtn('#submitBtn', 0);
                        $('#restoreForm')[0].reset(); // Reset the form
                        $('#importModalRestore').modal('hide');

                        // Hide the progress container
                        $('#progressContainer').hide();
                        const url = route('backup.index');
                        window.location.href = url;
                    },
                    error: function(xhr, status, error) {
                        displayErrorMessage(error);
                        processingBtn('#submitBtn', 0);

                        // Hide the progress container
                        $('#progressContainer').hide();
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Function to show/hide day selection based on frequency
            function toggleDaySelection() {
                if ($('#backup_frequency').val() == '3') {
                    $('#day-selection').show(); // Show the day dropdown when Weekly is selected
                } else {
                    $('#day-selection').hide(); // Hide it for other options
                }
            }

            // Initialize the visibility on page load
            toggleDaySelection();

            // Toggle the day selection when the frequency is changed
            $('#backup_frequency').on('change', function() {
                toggleDaySelection();
            });
        });
    </script>
@endsection
