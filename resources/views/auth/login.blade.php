@extends('layouts.auth')
@section('title')
    {{ __('messages.login.login') }}
@endsection
@section('content')


    <div class="container-fluid " style="width: 100%;">
        <div class="row justify-content-center" {{-- style="width: 100%;border-radius:10px;background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);" --}}>
            {{-- <div class="col-md-6">

                <div class="row justify-content-center">
                    <div class="col-md-10 mt-4">
                        <p class="text-center text-white">Lorem Ipsum is simply dummy text of the printing and typesetting
                            industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                        </p>
                    </div>

                </div>

                <div class="row mt-4">
                    <!-- ERP icons, placeholder -->
                    <div class="col-12 text-center">
                        <img src="{{ asset('img/login_info_image.png') }}" alt="ERP icons" style="width: 70%;margin:auto;">
                    </div>
                </div><br>
                <div class="row justify-content-center">
                    <p class="text-white mt-4">For any support, please contact Help Center</p>
                </div>

            </div> --}}
            <div class="col-md-7 bg-white" style=" border-radius:8px;">
                <div class="card-body bg-white">
                    <div class="row">

                        @include('flash::message')
                        <img src="{{ asset('img/company/company_logo.png') }}" alt="logo" width="196"
                            style="box-shadow:none;margin:auto;" style="margin: auto" class="shadow-light">
                        {{-- <h4>{{ __('messages.login.login') }}</h4> --}}

                    </div>
                    <br>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="font-weight-bold" for="email">{{ __('messages.login.email') }}</label>

                            <input aria-describedby="emailHelpBlock" id="email" type="email"
                                class="form-control{{ $errors->has('email') ? 'is-invalid' : '' }}" name="email"
                                tabindex="1"
                                value="{{ Cookie::get('email') !== null ? Cookie::get('email') : old('email') }}" autofocus
                                required style="border-radius: 10px;">
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-block">
                                <label for="password"
                                    class="control-label font-weight-bold">{{ __('messages.login.password') }}</label><span
                                    class="text-danger">*</span>
                                <div class="float-right">

                                </div>
                            </div>
                            <input aria-describedby="passwordHelpBlock" id="password" type="password"
                                class="form-control{{ $errors->has('password') ? 'is-invalid' : '' }}" name="password"
                                value="{{ Cookie::get('password') !== null ? Cookie::get('password') : null }}"
                                tabindex="2" required style="border-radius: 10px;">
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                                            id="remember" {{ Cookie::get('remember') !== null ? 'checked' : '' }}>
                                        <label class="custom-control-label" style="color:#28ace2 ;"
                                            for="remember">{{ __('messages.login.remember_me') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6   ">
                                <a href="{{ route('password.request') }}" class="float-right mr-2" style="color:#28ace2 ;">
                                    {{ __('messages.login.forgot_password') }}
                                </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block " tabindex="4"
                                style="background: #28ace2 !important;border:none;border-radius:55px;">
                                {{ __('messages.login.login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
