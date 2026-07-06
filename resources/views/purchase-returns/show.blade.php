@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-orders.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/sales/view-as-customer.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/estimates/estimates.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.purchase-orders.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                @if($estimate->status !== \App\Models\Estimate::STATUS_DECLINED && $estimate->status !== \App\Models\Estimate::STATUS_EXPIRED)
                    <a href="{{ route('purchase-returns.edit', ['return' => $estimate->id]) }}"
                       class="btn btnWarning text-white mr-2 form-btn">{{ __('messages.common.edit') }}</a>
                @endif
                <a href="{{ route('purchase-returns.index') }}" class="btn btn-primary form-btn">{{ __('messages.common.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">

                    @include('purchase-returns.show_fields')
                </div>
            </div>
        </div>
        @include('tasks.templates.templates')
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')

@endsection
