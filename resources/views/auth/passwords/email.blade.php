@extends('layouts.auth')
@section('title')
    {{ __('messages.email.forgot_password') }}
@endsection
@section('content')
    <div class="container-fluid " style="width: 100%;">
        <div class="row justify-content-center"
            {{-- style="width: 100%;border-radius:10px;background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);" --}}
            >
            {{-- <div class="col-md-6">

                <div class="row justify-content-center">
                    <div class="col-md-9 mt-4">
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
                    <div class="row">

                        <div class=" col text-center">
                            <h4 style="color: #28ace2 ;">{{ __('messages.login.forgot_password') }}</h4>
                        </div>
                        <p class="text-center">{{ __('messages.login.forget_text') }}</p>
                    </div>
                    <div class="row mt-2">
                        <form method="POST" action="{{ route('password.email') }}" style="width: 100%;">
                            @csrf
                            <div class="form-group">
                                <label for="email">{{ __('messages.login.email') }}</label><span
                                    class="text-danger">*</span>
                                <input id="email" type="email" style="border-radius:55px;"
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                    tabindex="1" value="{{ old('email') }}" autofocus required >
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>

                                <br><br>
                                <button type="submit" class="btn btn-primary btn-lg btn-block " tabindex="4"
                                    style="border-radius:55px;background: #28ace2 !important;border:none;font-size:16px;">
                                    {{ __('messages.email.send_reset_link') }}
                                </button>

                                <div class="mt-5 text-muted text-center">
                                    {{ __('messages.reset.recalled_your_login_info') }} <a class="txtPrimary"
                                        href="{{ route('login') }}">{{ __('messages.reset.sign_in') }}</a>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
