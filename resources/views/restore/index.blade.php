@extends('layouts.app')
@section('title')
    Database Restore
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>Database Restore</h1>
            @can('restore')
                <div class="section-header-breadcrumb float-right">
                    <div class="card-header-action mr-3 select2-mobile-margin">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModalRestore">
                            Import
                        </button>
                    </div>
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
                    @can('view_restore')
                        @include('restore.table')
                    @endcan
                </div>
            </div>
        </div>


    </section>
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
    <!-- AJAX script -->
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
@endsection
