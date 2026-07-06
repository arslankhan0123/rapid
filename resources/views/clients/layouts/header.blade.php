<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <a href="{{ route('clients.dashboard') }}" class="navbar-brand sidebar-gone-hide">{{ config('app.name') }}</a>
    <div class="navbar-nav">
        <a href="#" class="nav-link sidebar-gone-show" data-toggle="sidebar"><i class="fas fa-bars"></i></a>
    </div>
    <form class="form-inline ml-auto">
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                                                     class="nav-link notification-toggle nav-link-lg"
                                                     data-toggle="tooltip"
                                                     title="{{__('messages.notification.notifications')}}"> <i
                        class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right" id="notification">
                <div class="dropdown-header">
                    <div class="row justify-content-between">
                        <div class="px-3">{{__('messages.notification.notifications')}}</div>
                        <div class="px-3" id="allRead">
                            <a href="#"
                               class="text-decoration-none">{{__('messages.notification.mark_all_as_read')}}</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown-list-content dropdown-list-icons notification-content"
                     style="overflow-y:auto !important; ">
                    <div class="empty-state empty-notification d-none" data-height="300" style="padding: 0px 40px;">
                        <div class="empty-state-icon">
                            <i class="fas fa-question mt-4"></i>
                        </div>
                        <h2>{{__('messages.notification.empty_notifications')}}</h2>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    @if(session('impersonated_by'))
        <a href="{{ route('contacts.impersonate.leave') }}"
           class="mr-3 text-warning-all" data-toggle="tooltip"
           title="{{__('messages.user.return_to_admin')}}">
            <i class="fas fa-user-check return_to_admin"></i>
        </a>
    @endif
    <ul class="navbar-nav navbar-right">
        <li class="dropdown"><a href="#" data-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ getLoggedInUser()->image_url ?? '' }}"
                     class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">{{ getLoggedInUser()->full_name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('clients.profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i>{{__('messages.edit_profile.edit_profile')}}
                </a>
                <a href="#" data-toggle="modal" data-target="#changePasswordModal" data-id="{{ getLoggedInUserId() }}"
                   class="dropdown-item has-icon">
                    <i class="fas fa-lock"></i>{{__('messages.edit_profile.change_password')}}
                </a>
                @if(getLoggedInUser()->contact->primary_contact == 1)
                    <a href="{{ route('clients.company-details') }}" class="dropdown-item has-icon">
                        <i class="fas fa-info-circle"></i>{{ __('messages.company.details') }}
                    </a>
                @endif
                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#changeLanguageModal"
                   data-id="{{ getLoggedInUserId() }}">
                    <i class="fa fa-language mr-2"></i> {{ __('messages.user.change_language') }}</a>
                <div class="dropdown-divider"></div>
                <a href="#"
                   onclick="event.preventDefault(); localStorage.clear(); document.getElementById('logout-form').submit();"
                   class="dropdown-item has-icon text-danger">
                    <i class="fas fa-sign-out-alt"></i>{{__('messages.edit_profile.logout')}}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="post" class="d-none">
                    {{ csrf_field() }}
                </form>
            </div>
        </li>
    </ul>
</nav>
@include('clients.layouts.menu')

