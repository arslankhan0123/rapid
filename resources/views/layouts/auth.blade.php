<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title') | {{ config('app.name') }}</title>

    <!-- General CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet" type="text/css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/components.css') }}">
    @yield('page_css')
    <style>
        #email:focus,
        #password:focus {
            border-color: #0072ff;
            /* Change border color to red */
            box-shadow: 0 0 5px #0072ffed;
            /* Optional: adds a red shadow */
        }

        .txtPrimary {
            color: #0072ff;
        }
    </style>
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-11">
                        <div class="login-brand">

                        </div>
                        @yield('content')
                        {{-- <div class="simple-footer">
                            All rights reserved &copy; {{ date('Y') }} {{ getAppName() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>

    <!-- JS Libraies -->

    <!-- Template JS File -->
    <script src="{{ asset('assets/web/js/stisla.js') }}"></script>
    <script src="{{ asset('assets/web/js/scripts.js') }}"></script>

    <!-- Page Specific JS File -->
</body>
@yield('page_scripts')

</html>
