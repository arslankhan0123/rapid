@extends('layouts.app')
@section('title')
    {{__('messages.translation_manager')}}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{__('messages.translation_manager')}}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('translation-manager.index') }}"
                   class="btn btn-primary addLanguageModal form-btn float-right-mobile">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            @include('error')
            <div class="card">
                {{ Form::open(['route' => ['language.translation.update', $id], 'method' => 'post']) }}
                @include('translation_manager.fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        let languageName = "{{ $selectedLang }}"
        let fileName = "{{ $selectedFile }}"
        let url = "{{ route('language.translation', $id).'?' }}"
    </script>
    <script src="{{mix('assets/js/language_translate/language_translate.js')}}"></script>
@endsection
