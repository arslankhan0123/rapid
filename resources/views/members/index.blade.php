@extends('layouts.app')
@section('title')
    {{ __('messages.members') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header m-section item-align-right">
            <h1>{{ __('messages.members') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('is_enable', $memberStatus, 2, ['id' => 'memberStatus', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            </div>
            <div class="float-right">
                @can('create_users')
                    <a href="{{ route('members.create') }}" class="btn btn-primary form-btn">{{ __('messages.member.add') }}</a>
                @endcan

            </div>
        </div>
        @include('flash::message')
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('members')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let getLoginUserId = "{{ getLoggedInUserId() }}";
    </script>
    <script src="{{ mix('assets/js/members/member.js') }}"></script>
@endsection
