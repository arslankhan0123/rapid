@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-items.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.purchase-items.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('purchase-items.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.assets.list') }}</i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.common.code')) }}</strong>
                            <p style="color: #555;">{{ $category->code ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.common.barcode')) }}</strong>
                            <p style="color: #555;">{{ $category->barcode ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.short_name')) }}</strong>
                            <p style="color: #555;">{{ $category->name }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.full_name')) }}</strong>
                            <p style="color: #555;">{{ $category->name }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.cost_price')) }}</strong>
                            <p style="color: #555;">{{ number_format($category->cost_price ?? 0, 2) }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.price')) }}</strong>
                            <p style="color: #555;">{{ number_format($category->price ?? 0, 2) }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.stock')) }}</strong>
                            <p style="color: #555;">{{ $category->stock ?? 0 }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.common.groups')) }}</strong>
                            <p style="color: #555;">{{ $category->group->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.common.sub_categories')) }}</strong>
                            <p style="color: #555;">{{ $category->subCategory->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.common.categories')) }}</strong>
                            <p style="color: #555;">{{ $category->category->name ?? '' }}</p>
                        </div>

                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.unit')) }}</strong>
                            <p style="color: #555;">{{ $category->unit?->title ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.brand')) }}</strong>
                            <p style="color: #555;">{{ $category->brand?->title ?? '' }}</p>
                        </div>
                        {{-- <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.size')) }}</strong>
                            <p style="color: #555;">{{ $category->size?->title ?? '' }}</p>
                        </div> --}}
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.color')) }}</strong>
                            <p style="color: #555;">{{ $category->color?->title ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong> {{ Form::label('title', __('messages.purchase-items.image')) }}</strong><br>
                            @if($category->image)
                                <a href="javascript:void(0)" class="view-image-btn" data-image-url="{{ asset($category->image) }}" data-title="{{ $category->full_name ?? $category->name ?? 'Image' }}">
                                    <img src="{{ asset($category->image) }}" alt="Item Image" width="100" style="object-fit:cover; border-radius:4px;">
                                </a>
                            @endif
                        </div>

                        <div class="form-group col-sm-12 mb-0">
                            <strong>{{ Form::label('description', __('messages.common.description')) }}</strong>
                            <div style="color: #555;"> {!! $category->description !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" id="previewModalImage" class="img-fluid" alt="Preview Image" style="max-height: 400px; object-fit: contain;">
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
@section('scripts')
    <script>
        $(document).on('click', '.view-image-btn', function() {
            let imageUrl = $(this).data('image-url');
            let title = $(this).data('title');
            $('#imagePreviewModalLabel').text(title);
            $('#previewModalImage').attr('src', imageUrl);
            $('#imagePreviewModal').appendTo("body").modal('show');
        });
    </script>
@endsection
