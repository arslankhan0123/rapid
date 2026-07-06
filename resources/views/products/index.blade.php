@extends('layouts.app')
@section('title')
    {{ __('messages.products.products') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.products.products') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('group', $categories??[], null, ['id' => 'filter_category', 'class' => 'form-control select2' ,'style'=>"max-width:300px;", 'placeholder' => "Select Category"]) }}
                </div>
            </div>

            <div class="float-right">
                <a href="#" class="btn btn-primary form-btn" data-toggle="modal"
                    data-target="#addModal">{{ __('messages.common.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('products.table')

                </div>
            </div>
        </div>
    </section>
    @include('products.templates.templates')
    @include('products.add_modal')
    @include('products.edit_modal')
    @include('products.product_group_modal')
    @include('products.edit_product_group_modal')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ asset('assets/js/products/products.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#serviceGroup,#editserviceGroup").select2({
                width: "100%"
            });
        });

        $(document).on('click', '.view-btn', function(event) {
            event.preventDefault(); // Prevent the default link behavior
            var productId = $(this).data('id'); // Get the product ID from the data attribute
            var viewUrl = "{{ route('products.view', ':id') }}".replace(':id', productId); // Construct the URL
            window.location.href = viewUrl; // Navigate to the constructed URL
        });
    </script>
@endsection
