@extends('layouts.app')

@section('title')
    Financial Year Management
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Financial Year Ending</h1>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body text-center">
                    <p class="alert alert-warning">
                        You can end Financial Year at the end of Financial Year. If you end Financial Year, all your closing
                        balance will be added in opening balance for the new Financial Year.
                    </p>
                    <button class="btn btn-danger" id="endYearBtn">End Your Financial Year</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <div class="d-flex align-items-center flex-column">
                        <i class="fas fa-exclamation-circle text-warning mb-2" style="font-size: 100px;"></i>
                        <h3 class="modal-title text-center" id="confirmationModalLabel">
                            Year Ending
                        </h3>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="position: absolute; top: 10px; right: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Are You Sure?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-success" id="confirmEndYearBtn">Yes</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Show confirmation modal when the button is clicked
            $('#endYearBtn').click(function() {
                $('#confirmationModal').modal('show');
            });

            // Handle confirmation button click
            $('#confirmEndYearBtn').click(function() {
                // Add your AJAX call or form submission logic here
                $('#confirmationModal').modal('hide');
            });
        });
    </script>
@endsection
