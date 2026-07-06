@extends('layouts.app')
@section('title')
    {{ __('messages.documents.add_document') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.documents.add_document') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('documents.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('layouts.errors')
                    {!! Form::open(['route' => 'documents.store', 'id' => 'addDocumentForm', 'files' => true]) !!}
                    @include('documents.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Add new document row
            $('#addDocument').click(function() {
                let newRow = `
                <div class="form-group row document-row">
                    <div class="col-md-5">
                        <label class="form-label">{{ __('messages.documents.document') }}: <span class="text-danger">*</span></label>
                        <input type="file" name="document[]" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx,.csv" required>
                        <small class="form-text text-muted">
                            {{ __('messages.documents.allowed_types') }}: PDF, Word, Docx, Doc, xls, JPG, PNG, Excel, CSV.
                            {{ __('messages.documents.max_size') }}: 10MB
                        </small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">{{ __('messages.documents.description') }}:</label>
                        <textarea name="description[]" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeDocument">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
                $('#documentContainer').append(newRow);
            });

            // Remove document row
            $(document).on('click', '.removeDocument', function() {
                $(this).closest('.document-row').remove();
            });
        });

        $(document).ready(function() {
            // Initialize Select2 with multiple selection only for admin
            @if (auth()->id() === 1)
                $('#user_id').select2({
                    placeholder: "{{ __('messages.documents.select_users') }}",
                    allowClear: true,
                    width: '100%'
                });
            @endif
        });
    </script>
@endsection
