{{-- @dd(auth()->user()->getAllPermissions()->toArray()) --}}
<!DOCTYPE html>
<html lang="{{ lang() }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title') | {{ config('app.name') }} </title>
    <link rel="shortcut icon" href="{{ getAppFavicon() }}" type="image/x-icon" sizes="16x16">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @routes

    <!-- General CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link href="{{ asset('assets/css/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" /> --}}
    <!-- CSS Libraries -->


    <link rel="stylesheet" href="{{ getAppFavicon() ?? asset('favicon.ico') }}">
    @yield('page_css')
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/components.css') }}">
    @yield('css')
    <link href="{{ mix('assets/css/infy-loader.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rapid.css') }}">
    @if (lang() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/rtl.css') }}">
    @endif
    <style>
        .side-menus a i {
            color: white !important;
        }

        .navbar-bg {
            background: #28ace2 !important;
        }

        #sidebar-wrapper {
            background: #28ace2 !important;
        }

        .btn-primary {
            background: #28ace2 !important;
            /* background: red !important; */
            border: none;
            height: 40px;
            line-height: 37px;
            min-width: 100px;
            font-size: 18px !important;
        }



        .btnWarning {
            background: orange !important;
            /* background: red !important; */
            border: none;
            height: 40px;
            line-height: 37px;
            min-width: 100px;
            font-size: 18px !important;
        }

        .btnSecondary {
            background: #666 !important;
            /* background: red !important; */
            border: none;
            height: 40px;
            line-height: 32px;
            min-width: 100px;
            font-size: 18px !important;
        }

        .badge-primary,
        .bg-primary {
            background: #28ace2 !important
        }

        .main-sidebar,
        .sidebar-brand {
            background: #28ace2 !important;
        }

        .side-menus {
            color: white !important;
        }

        .menu-text-wrap {
            color: white !important;
        }

        /* .menu-text-wrap ,.active{
            color: red !important;
        } */
        .side-menus.active a {
            color: #fff !important;
            /* Text color when active */
            background-color: rgb(16, 144, 194) !important;
            /* Background color when active */
        }

        .active a i,
        .active a i span {
            /* color:#007bff !important; */

        }


        .side-menus a:hover {
            background-color: #007bff !important;
        }

        .main-sidebar .sidebar-menu li.menu-header,
        .main-sidebar .sidebar-menu li a {
            color: #fff !important;
        }

        .submenu a i {
            color: white !important;
            font-size: 20px;

        }


        .submenu a span {
            color: rgb(255, 255, 255) !important;
            font-weight: bold;
            font-size: 18px;

        }

        #btnSave,
        .btnCancel,
        #saveAsDraft,
        #export-link {
            height: 40px !important;
            line-height: 32px !important;


        }

        .fa-trash-alt {
            color: red !important;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="infy-loader" id="overlay-screen-lock">
            @include('loader')
        </div>
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                @include('layouts.header')
            </nav>
            <div class="main-sidebar">
                @include('layouts.sidebar')
            </div>
            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
            <foote class="main-footer">
                @include('layouts.footer')
            </foote>
        </div>
    </div>
    @include('user_profile.change_password_modal')
    @include('user_profile.change_language_modal')
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('assets/js/autonumeric/autoNumeric.min.js') }}"></script>
    <script src="{{ asset('assets/web/js/stisla.js') }}"></script>
    <script src="{{ asset('assets/web/js/scripts.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/handlebars/handlebars.js') }}"></script>
    <script src="{{ asset('messages.js') }}"></script>
    <script src="{{ asset('assets/js/rapid.js') }}"></script>
    <script>
        let showNoDataMsg = "{{ __('messages.common.no_data_available_in_table') }}"
        let noSearchResults = "{{ __('messages.common.search_results') }}"
        let noMatchingRecordsFound = "{{ __('messages.no_matching_records_found') }}"
        let searchCustomerUrl = "{{ route('customers.search.customer') }}"
        let baseUrl = "{{ url('/') }}/"
        let currentUrlName = "{{ Request::url() }}"
        let yesMessages = "{{ __('messages.common.yes') }}"
        let noMessages = "{{ __('messages.common.no') }}"
        let deleteHeading = "{{ __('messages.common.delete') }}"
        let deleteConfirm = "{{ __('messages.common.delete_confirm') }}"
        let toTypeDelete = "{{ __('messages.common.to_delete_this') }}"
        let deleteWord = "{{ __('messages.common.delete') }}"
        let searchPlaceholder = "{{ __('messages.common.search') }}"
        let defaultCountryCodeValue = "{{ getDefaultCountryCode() }}"
        let changePasswordUrl = "{{ route('change.password') }}"
    </script>
    @yield('page_scripts')
    <script>
        let currentLocale = "{{ \Illuminate\Support\Facades\Config::get('app.locale') }}"
        if (currentLocale == '') {
            currentLocale = 'en'
        }
        Lang.setLocale(currentLocale)
        let currentCurrencyClass = "{{ getCurrencyClass() }}";
        (function($) {
            $.fn.button = function(action) {
                if (action === 'loading' && this.data('loading-text')) {
                    this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true)
                }
                if (action === 'reset' && this.data('original-text')) {
                    this.html(this.data('original-text')).prop('disabled', false)
                }
            }
        }(jQuery));
        $(document).ready(function() {
            $('.alert').delay(5000).slideUp(300);
        });
    </script>
    @yield('scripts')
    <script>
        let loggedInUserId = "{{ getLoggedInUserId() }}";
        let ajaxCallIsRunning = false;
        let pdfDocumentImageUrl = "{{ asset('img/attachments_img/pdf.png') }}";
        let docxDocumentImageUrl = "{{ asset('img/attachments_img/doc.png') }}";
        let blockedAttachmentUrl = "{{ asset('img/attachments_img/blocked.png') }}";
        let customersUrl = '{{ route('customers.index') }}';
        let changeLanguageUrl = "{{ route('change.language') }}";
    </script>
    <script src="{{ mix('assets/js/user-profile/user-profile.js') }}"></script>
    <script src="{{ mix('assets/js/notifications/notification.js') }}"></script>



    <script>
        $(document).ready(function() {
            $('input, select').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent form submission on Enter
                    let form = $(this).closest('form');
                    let inputs = form.find('input, select');
                    let index = inputs.index(this);

                    // If the next input is Select2
                    if ($(inputs[index + 1]).hasClass('select2')) {
                        $(inputs[index + 1]).on('select2:select', function() {
                            inputs.eq(index + 2).focus();
                        });
                    } else if ($(inputs[index + 1]).hasClass('summernote-simple')) {
                        $(inputs[index + 1]).summernote('focus');
                    } else if ($(inputs[index + 1]).attr('type') === 'file') {
                        // Open the file dialog for file input
                        $(inputs[index + 1]).trigger('click');
                    } else {
                        inputs.eq(index + 1).focus();
                    }
                }
            });

            // Handle Enter in Select2 field to trigger focus on the next field
            $('.select2').on('select2:select', function(e) {
                let form = $(this).closest('form');
                let inputs = form.find('input, select, textarea');
                let index = inputs.index(this);
                inputs.eq(index + 1).focus();
            });

            // Handle file input 'Enter' key
            $('input[type="file"]').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $(this).trigger('click'); // Trigger the file dialog
                }
            });
        });
    </script>
</body>

</html>
