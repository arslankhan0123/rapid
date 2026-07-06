@extends('layouts.app')
@section('title')
    {{ __('messages.member.member_details') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.member.member_details') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <a href="{{ route('members.edit', ['member' => $member->id]) }}"
                   class="btn btnWarning text-white mr-2 form-btn">{{ __('messages.common.edit') }}
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn">{{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('members.show_fields')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        let member_id = "{{ $member->id }}";
    </script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    @stack('page-scripts')
@endsection
