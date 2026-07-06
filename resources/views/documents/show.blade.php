@extends('layouts.app')
@section('title')
    {{ __('messages.documents.view_document') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.documents.view_document') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('documents.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.documents.document') }}:</label>
                                <p>{{ $document->document }}</p>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.documents.uploaded_by') }}:</label>
                                <p>{{ $document->user->name }}</p>
                            </div>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('messages.documents.description') }}:</label>
                                <p>{{ $document->description ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.documents.uploaded_at') }}:</label>
                                <p>{{ $document->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="{{ route('documents.download', $document->id) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i> {{ __('messages.documents.download') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
