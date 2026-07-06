@extends('layouts.auth')

@section('content')
    <div class="d-flex justify-content-center align-items-center ">
        <div class="card shadow-lg p-4" style="width: 400px; border-radius: 10px;">
            <div class="card-body text-center">
                <h3 class="mb-3">Enter OTP</h3>
                <p class="text-muted">We have sent a 6-digit OTP to your email.</p>

                <form action="{{ route('otp.verify.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('email') }}">

                    <div class="mb-3">
                        <label class="form-label">OTP Code</label>
                        <input type="text" name="otp_code" class="form-control text-center"
                            style="letter-spacing: 3px; font-size: 20px;" maxlength="6" required autofocus>
                        @error('otp_code')
                            <p class="text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Verify OTP</button>


                </form>
            </div>
        </div>
    </div>
@endsection
