@extends('layouts.app')
@section('title')
    {{ __('messages.revokes.view_revoke') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.revokes.view_revoke') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('revokes.index') }}" class="btn btn-primary form-btn float-right">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.revokes.employee') }}:</label>
                                <p class="form-control-static">{{ $revoke->employee->name }}
                                    ({{ $revoke->employee->iqama_no }})</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.revokes.termination_date') }}:</label>
                                <p class="form-control-static">
                                    {{ $revoke->termination->date ? \Carbon\Carbon::parse($revoke->termination->date)->format('d-m-Y') : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.revokes.revoke_date') }}:</label>
                                <p class="form-control-static">
                                    {{ $revoke->date ? \Carbon\Carbon::parse($revoke->date)->format('d-m-Y') : '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.revokes.status') }}:</label>
                                <p class="form-control-static">{{ $revoke->status ? 'Active' : 'Inactive' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('messages.revokes.reason') }}:</label>
                                <div class="form-control-static">{!! $revoke->reason !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
