@extends('layouts.app')
@section('title')
    {{ __('messages.assets.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.assets.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('assets.index') }}" class="btn btn-primary form-btn">{{ __('messages.assets.list') }}</i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.branches.name')) }}</strong>
                                <p style="color: #555;">{{ $asset->branch?->name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.assets.asset_name')) }}</strong>
                                <p style="color: #555;">{{ $asset->name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('title', __('messages.assets.asset_company_code')) }}</strong>
                                <p style="color: #555;">{{ $asset->company_asset_code ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong> {{ Form::label('category_id', __('messages.assets.category')) }}</strong>
                                <p style="color: #555;">{{ $asset->category?->title ?? '' }}</p>
                            </div>

                        </div>
                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('is_working', __('messages.assets.is_working')) }}</strong>
                                <p style="color: #555;">{{ $asset->is_working }}</p>
                            </div>
                        </div>

                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('company_name', __('messages.assets.company')) }}</strong>
                                <p style="color: #555;">{{ $asset->company_name }}</p>

                            </div>
                        </div>
                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>
                                    {{ Form::label('employee_id', __('messages.assets.asset_employee')) }}</strong>
                                <p style="color: #555;">{{ $asset->employee?->name ?? '' }}</p>
                            </div>

                        </div>
                        <div class="ol-sm- 12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>
                                    {{ Form::label('employee_id', 'Supplier') }}</strong>
                                <p style="color: #555;">{{ $asset->supplier?->company_name ?? '' }}</p>
                            </div>

                        </div>

                        <div class="col-sm-12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('purchase_date', __('messages.assets.asset_purchase_date')) }}</strong>
                                <p style="color: #555;">
                                    {{ \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('warranty_end_date', __('messages.assets.asset_warranty_end_date')) }}</strong>
                                <p style="color: #555;">
                                    {{ \Carbon\Carbon::parse($asset->warranty_end_date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="ol-sm-12 col-md-6">
                            <strong>{{ Form::label('title', __('messages.assets.asset_manufacturer')) }}</strong>
                            <p style="color: #555;">{{ $asset->manufacturer }}</p>

                        </div>
                        <div class="col-sm-12 col-md-6">
                            <strong>{{ Form::label('title', __('messages.assets.asset_invoice_number')) }}</strong>
                            <p style="color: #555;">{{ $asset->invoice_number }}</p>

                        </div>


                        <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                        <div class="col-md-6">
                            <strong> {{ Form::label('title', __('messages.assets.asset_serial_number')) }}</strong>
                            <p style="color: #555;">{{ $asset->serial_number }}</p>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('qty', 'Quantity') }}</strong>
                                <p style="color: #555;">{{ $asset->qty }}</p>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('price', 'Price') }}</strong>
                                <p style="color: #555;">{{ number_format($asset->price, 2) }}</p>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('total', 'Total') }}</strong>
                                <p style="color: #555;">{{ number_format($asset->total, 2) }}</p>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <strong> {{ Form::label('image', __('messages.assets.asset_image')) }}</strong>
                            <div id="edit_image-preview" style="display: {{ $asset->image ?? 'none' }} ;">
                                <img id="edit_preview-img" src="{{ asset('uploads/public/images/' . $asset->image) }}"
                                    alt="Image Preview" class="circle-image"
                                    style="width:50%; height: auto;border-radius:5px;" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="ol-sm-12 col-md-6">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                {{ Form::label('title', __('messages.assets.asset_note')) }}
                                <div style="color: #555;"> {!! $asset->asset_note !!}</div>
                            </div>
                        </div>
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
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
