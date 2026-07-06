@extends('layouts.app')
@section('title')
    {{ __('messages.estimate.estimate_details') }}
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
            <h1>{{ __('messages.estimate.estimate_details') }}</h1>
            <div class="section-header-breadcrumb float-right">
                @if($estimate->status !== \App\Models\Estimate::STATUS_DECLINED && $estimate->status !== \App\Models\Estimate::STATUS_EXPIRED)
                    <a href="{{ route('estimates.edit', ['estimate' => $estimate->id]) }}"
                       class="btn btn-warning mr-2 form-btn">{{ __('messages.common.edit') }}</a>
                @endif
                <a href="{{ route('estimates.index') }}" class="btn btn-primary form-btn">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @include('estimates.show_fields')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let estimateId = "{{ $estimate->id }}";
        let invoiceUrl = "{{ route('invoices.index') }}";
        let taskUrl = "{{ route('tasks.index') }}";
        let statusArray = JSON.parse('@json($status)');
        let priorities = JSON.parse('@json($priorities)');
        let ownerId = "{{ $estimate->id }}";
        let ownerType = 'App\\Models\\Estimate';
        let memberUrl = "{{ route('members.index') }}";
    </script>
    <script src="{{ mix('assets/js/tasks/tasks.js')}}"></script>
    <script src="{{ mix('assets/js/estimates/show-page.js') }}"></script>
@endsection
