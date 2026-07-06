@extends('layouts.app')
@section('title')
    {{ __('messages.print-labels.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .ui-autocomplete {
            background-color: #fff !important;
            /* White background */
            border: 1px solid #ced4da !important;
            height: 150px;
            overflow-y: auto !important;
            /* Scroll if too many suggestions */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
            z-index: 999;
            width: 400px;
            list-style-type: none !important;
            /* Remove bullet points */
            padding: 0 !important;
            /* Remove padding */
            margin: 0 !important;
            /* Remove margin */
        }

        /* Style for individual list items in the dropdown */
        .ui-menu .ui-menu-item {
            padding: 0.375rem 0.75rem !important;
            /* Adjust padding for list items */
            cursor: pointer;
        }

        /* Optional: Hover effect for list items */
        .ui-menu .ui-menu-item:hover {
            background-color: #36bfff !important;
            color: white;
            /* Light gray hover background */
        }

        .quantity {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity button {
            width: 30px;
            height: 30px;
            margin: 0 5px;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            font-size: 16px;
        }

        .pdf-label-wrapper {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            background-color: red;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.print-labels.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
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

                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group col-md-6 mb-1 d-flex align-items-center pl-0 pr-0">

                                {{ Form::text('item', null, ['class' => 'form-control form-control-sm', 'required', 'id' => 'inptItem', 'autocomplete' => 'off', 'placeholder' => 'ItemCode/Barcode/Item Name']) }}
                            </div>
                        </div>
                    </div>
                    {{ Form::open(['id' => 'printLabelsItems']) }}
                    <table class="table table-striped" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Barcode</th>
                                <th>Item Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Item rows will be appended here -->
                        </tbody>
                    </table>
                    <div class="row justify-content-end pr-3">
                        <button type="button" class="btn btn-primary" id="previewButton"
                            style="line-height: 30px;">Preview</button>
                        {{ Form::close() }}
                    </div>


                </div>
            </div>
        </div>

    </section>
    @include('print-labels.view_labels')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2@11.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>

    <script>
        function progressBTN(button, isLoading, submit = "Preview") {
            if (isLoading) {
                button.prop('disabled', true); // Disable the button
                button.html('Loading... <i class="fa fa-spinner fa-spin"></i>'); // Show loading spinner
            } else {
                button.prop('disabled', false); // Enable the button
                button.html(submit); // Reset button text
            }
        }



        function addToItemTable(item) {
            // Check if item already exists in the table by code
            let existingRow = $(`#itemsTable tbody tr[data-code="${item.code}"]`);

            if (existingRow.length > 0) {
                // Item already exists, update the quantity
                let quantityInput = existingRow.find('.quantity-input');
                let currentQuantity = parseInt(quantityInput.val());

                if (isNaN(currentQuantity)) {
                    // If it's NaN, set the quantity to 1 as a fallback
                    currentQuantity = 1;
                }

                // Increase quantity by 1
                quantityInput.val(currentQuantity + 1);
            } else {
                // Item doesn't exist, add a new row to the table
                let rowHtml = `
            <tr data-code="${item.code}">
                    <td>${item.code}</td>
                    <td>${item.barcode}</td>
                    <td>${item.name}</td>

                <td class="quantity">
                    <a class="btn btn-sm btn-danger decrease">
                        <i class="fas fa-minus"></i>
                    </a>
                    <input type="number" class="quantity-input form-control" value="1" min="1" style="width: 120px; text-align: center;" name="quantity[${item.id}]" />
                    <a class="btn btn-sm btn-success increase">
                        <i class="fas fa-plus"></i>
                    </a>
                </td>
                <td class="text-right">
                    <a class="btn btn-sm btn-danger remove ">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        `;
                $('#itemsTable tbody').append(rowHtml);
            }
        }

        // Handle Increase Quantity
        $('#itemsTable').on('click', '.increase', function() {
            let quantityInput = $(this).closest('td').find('.quantity-input');
            let currentQuantity = parseInt(quantityInput.val());

            if (isNaN(currentQuantity)) {
                // Fallback to 1 if NaN is encountered
                currentQuantity = 1;
            }

            quantityInput.val(currentQuantity + 1); // Increase quantity by 1
        });

        // Handle Decrease Quantity
        $('#itemsTable').on('click', '.decrease', function() {
            let quantityInput = $(this).closest('td').find('.quantity-input');
            let currentQuantity = parseInt(quantityInput.val());

            if (isNaN(currentQuantity)) {
                // Fallback to 1 if NaN is encountered
                currentQuantity = 1;
            }

            if (currentQuantity > 1) {
                quantityInput.val(currentQuantity - 1); // Decrease quantity by 1
            }
        });

        // Handle Remove Item from Table
        $('#itemsTable').on('click', '.remove', function() {
            $(this).closest('tr').remove(); // Remove the item row
        });





        $("#inptItem").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('print-labels.all-items') }}", // Laravel route to fetch items
                    type: "GET",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        let mappedData = $.map(data, function(item) {
                            return {
                                label: `${item.code} - ${item.barcode} - ${item.name}`,
                                value: item.name,
                                data: item
                            };
                        });
                        response(mappedData);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                let selectedItem = ui.item.data;
                addToItemTable(selectedItem); // Call the function to add the item to the table
                $(this).val(''); // Clear the input field
                return false;
            }
        });

        // Handle Preview Button Click
        $('#previewButton').on('click', function() {


            let isValid = true;
            let quantityInputs = $('#itemsTable').find('.quantity-input'); // Get all quantity input fields

            // Check if any quantity is 0 or if no item is in the table
            quantityInputs.each(function() {
                let quantity = $(this).val();
                if (quantity == 0 || quantity == '') { // If quantity is 0 or empty
                    isValid = false;
                    return false; // Exit the loop
                }
            });

            if ($('#itemsTable tbody tr').length === 0) {
                isValid = false;
            }

            // If the validation fails, show an alert and return
            if (!isValid) {
                displayErrorMessage("No Item Added to the list");
                return; // Prevent form submission
            }

            var formData = $('#printLabelsItems').serialize(); // Serialize form data


            progressBTN($('#previewButton'), true);

            $.ajax({
                url: "{{ route('print-labels.preview') }}", // Change this to the route where the data should be sent
                type: 'POST',
                data: formData,
                success: function(response) {
                    progressBTN($('#previewButton'), false);
                    if (response.pdfData) {
                        // Decode the base64 PDF string
                        var pdfData = response.pdfData;

                        // Set the PDF content into the iframe
                        var pdfUrl = 'data:application/pdf;base64,' + pdfData;
                        $('#pdfIframe').attr('src', pdfUrl);
                        if (response.htmlContent) {
                            $(".pdfHtml").empty();
                            $(".pdfHtml").append(response.htmlContent); // Append new HTML content
                        }
                        // Show the modal
                        $('#pdfModals').modal('show');

                        console.log(formData);
                    } else {
                        alert('Error generating the PDF.');
                    }
                },
                error: function(xhr, status, error) {
                    progressBTN($('#previewButton'), false);

                }
            });
        });


        function printPDF() {

            var formData = $('#printLabelsItems').serialize(); // Serialize form data
            formData += '&print=true';
            startLoader();
            $.ajax({
                url: "{{ route('print-labels.preview') }}", // Same route as before
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.data) {
                        displaySuccessMessage("Printing");
                        $("#pdfModals").modal("hide");
                    }
                    stopLoader();
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                    stopLoader();

                }
            });
        }
    </script>
@endsection
