@extends('layouts.app')
@section('title')
    {{ __('messages.articles') }}
@endsection
@section('page_css')
    <link href="{{ mix('assets/css/articles/articles.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header mobile-section-header">
            <h1>{{ __('messages.articles') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="mr-2 ipad-margin-left">
                    {{Form::select('article_group', $groupArr, null, ['id' => 'filterArticleGroup', 'class' => 'form-control','placeholder' =>__('messages.placeholder.select_article_group')]) }}
                </div>
            </div>
            <div class="float-right mr-2">
                {{Form::select('internal_article', $internalArticle, null, ['id' => 'filterInternalArticle', 'class' => 'form-control','placeholder' =>__('messages.placeholder.select_internal_article')]) }}
            </div>
            <div class="float-right mr-2">
                {{Form::select('disabled', $disabledArticle, null, ['id' => 'filterDisabledArticle', 'class' => 'form-control','placeholder' => __('messages.placeholder.select_status')]) }}
            </div>
            <div class="float-right">
                <a href="{{ route('articles.create') }}"
                   class="btn btn-primary form-btn text-nowrap">{{ __('messages.article.add') }} </a>
            </div>
        </div>
        @include('flash::message')
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('articles')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{mix('assets/js/articles/articles.js')}}"></script>
@endsection
