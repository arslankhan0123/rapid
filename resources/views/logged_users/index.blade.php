@extends('layouts.app')
@section('title')
    {{ __('messages.logged_users.logged_users') }}
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.logged_users.logged_users') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="card-header-action mr-3">
                    <span class="badge badge-success">
                        <i class="fas fa-circle text-success"></i>
                        {{ __('messages.logged_users.online_users') }}: {{ $onlineUsersCount }}
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            @if (auth()->user()->staff_member == 0)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user_filter">{{ __('messages.logged_users.filter_user') }}</label>
                        <select class="form-control select2" id="user_filter">
                            <option value="">{{ __('messages.common.all_users') }}</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="col-md-3">
                <div class="form-group">
                    <label for="status_filter">{{ __('messages.logged_users.filter_status') }}</label>
                    <select class="form-control select2" id="status_filter">
                        <option value="">{{ __('messages.common.all') }}</option>
                        <option value="online">{{ __('messages.logged_users.online') }}</option>
                        <option value="offline">{{ __('messages.logged_users.offline') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('logged_users.table')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection

@section('scripts')
    <script>
        let loggedUsersUrl = "{{ route('logged-users.index') }}";
        var permissions = {
            forceLogout: {{ auth()->user()->hasPermissionTo('force_logout_logged_users') ? 'true' : 'false' }}
        };


        $(document).ready(function() {
            // Initialize Select2
            $('#user_filter').select2({
                width: '100%'
            });
            $('#status_filter').select2({
                width: '100%'
            });

            let tbl = $('#loggedUsersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: loggedUsersUrl,
                    data: function(d) {
                        d.status = $('#status_filter').val();
                        d.user_id = $('#user_filter').length ? $('#user_filter').val() : null;
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTables AJAX Error:', {
                            xhr,
                            error,
                            code,
                            responseText: xhr.responseText
                        });
                        $('#loggedUsersTable_wrapper').prepend(
                            '<div class="alert alert-danger alert-dismissible">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<strong>Session Expired:</strong> You have been logged out. Redirecting to login...' +
                            '</div>'
                        );
                        setTimeout(function() {
                            window.location.href = "{{ route('login') }}";
                        }, 3000);
                    }
                },
                columns: [{
                        data: 'full_name',
                        name: 'user.first_name',
                        width: '15%'
                    },
                    {
                        data: 'user.email',
                        name: 'user.email',
                        width: '15%'
                    },
                    {
                        data: 'user.phone',
                        name: 'user.phone',
                        width: '10%'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address',
                        width: '10%'
                    },
                    {
                        data: 'country',
                        name: 'country',
                        width: '10%'
                    },
                    {
                        data: 'city',
                        name: 'city',
                        width: '10%'
                    },
                    // {
                    //     data: 'region',
                    //     name: 'region',
                    //     width: '10%'
                    // },
                    // {
                    //     data: 'address',
                    //     name: 'address',
                    //     width: '20%',
                    //     render: d => d || '-'
                    // },
                    {
                        data: 'login_at',
                        name: 'login_at',
                        render: data => moment(data).format('DD-MM-YYYY HH:mm:ss'),
                        width: '10%'
                    },
                    {
                        data: null,
                        render: function(row) {
                            if (row.status === 'offline' && row.logout_at) {
                                return moment(row.logout_at).format('DD-MM-YYYY HH:mm:ss');
                            } else {
                                return moment(row.last_activity_at).format('DD-MM-YYYY HH:mm:ss');
                            }
                        },
                        name: 'last_activity_at',
                        width: '10%'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '5%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        width: '8%',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let buttons = `<div class="btn-group" role="group">`;

                            // Location button hidden (commented out)
                            /*
                            <button type="button" class="btn btn-primary btn-sm view-location-btn"
                                    data-lat="${row.latitude || ''}"
                                    data-lng="${row.longitude || ''}"
                                    data-city="${row.city || ''}"
                                    data-region="${row.region || ''}"
                                    data-country="${row.country || ''}"
                                    data-address="${row.address || ''}">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                            */

                            // Force Logout button with permission check
                            if (permissions.forceLogout) {
                                buttons += `
                <button type="button" class="btn btn-danger btn-sm force-logout-btn" data-id="${row.id}">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            `;
                            }

                            buttons += `</div>`;
                            return buttons;
                        }
                    }

                ],
                order: [
                    [8, 'desc']
                ], // sort by login_at
                responsive: true,
                language: {
                    processing: "Loading logged users...",
                    emptyTable: "No logged users found",
                    error: "Error loading data"
                }
            });

            // Filters
            $('#user_filter').on('change', () => tbl.ajax.reload());
            $('#status_filter').on('change', () => tbl.ajax.reload());

            // Force logout
            $(document).on('click', '.force-logout-btn', function() {
                let userId = $(this).data('id');
                let button = $(this);

                if (confirm('Are you sure you want to force logout this user?')) {
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: `${window.location.origin}/admin/logged-users/${userId}/force-logout`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(result) {
                            alert(result.success ? (result.message ||
                                    'User logged out successfully') :
                                'Failed to logout user');
                            tbl.ajax.reload();
                        },
                        error: function(xhr) {
                            console.error('Force logout error:', xhr);
                            let message = (xhr.responseJSON && xhr.responseJSON.message) ||
                                'Error occurred while logging out user';
                            alert(message);
                        },
                        complete: function() {
                            button.prop('disabled', false).html(
                                '<i class="fas fa-sign-out-alt"></i>');
                        }
                    });
                }
            });

            // View location
            $(document).on('click', '.view-location-btn', function() {
                let lat = $(this).data('lat');
                let lng = $(this).data('lng');
                let city = $(this).data('city');
                let region = $(this).data('region');
                let country = $(this).data('country');
                let address = $(this).data('address');

                let locationText = address || [city, region, country].filter(Boolean).join(', ');
                let mapUrl = (lat && lng) ? `https://www.google.com/maps?q=${lat},${lng}` : null;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Location Details',
                        html: `
                        <div class="text-left">
                            <p><strong>Coordinates:</strong> ${lat && lng ? `${lat}, ${lng}` : 'Not available'}</p>
                            <p><strong>Address:</strong> ${locationText || 'Not available'}</p>
                            ${mapUrl ? `
                                                                    <p class="mt-2">
                                                                        <a href="${mapUrl}" target="_blank" class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-external-link-alt"></i> View on Map
                                                                        </a>
                                                                    </p>` : ''}
                        </div>
                    `,
                        icon: 'info',
                        confirmButtonText: 'Close'
                    });
                } else {
                    if (mapUrl) {
                        window.open(mapUrl, '_blank');
                    } else {
                        alert("No coordinates available for this user");
                    }
                }
            });

            // Auto refresh
            setInterval(() => {
                if ($.fn.DataTable.isDataTable('#loggedUsersTable')) {
                    try {
                        tbl.ajax.reload(null, false);
                    } catch (e) {
                        console.error('Auto refresh failed:', e);
                    }
                }
            }, 30000);
        });
    </script>

    <script>
        (function() {
            if (!('geolocation' in navigator)) {
                console.debug('Geolocation not supported');
                return;
            }

            if (sessionStorage.getItem('geo_sent') === '1') {
                console.debug('Geolocation already sent this session');
                return;
            }

            let attempts = 0;
            const maxAttempts = 3;

            function tryGetLocation() {
                attempts++;

                // Progressive accuracy requirements - start strict, then relax
                const accuracyThreshold = attempts === 1 ? 50 : (attempts === 2 ? 100 : 200);

                const options = {
                    enableHighAccuracy: attempts <= 2, // High accuracy for first 2 attempts
                    timeout: attempts === 1 ? 15000 : 10000, // More time for first attempt
                    maximumAge: 0 // Always get fresh location
                };

                console.debug(`Geolocation attempt ${attempts}/${maxAttempts}`, options);

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        console.debug('Geolocation success', {
                            accuracy: pos.coords.accuracy,
                            threshold: accuracyThreshold,
                            attempt: attempts
                        });

                        // Accept if accuracy is good enough or this is our last attempt
                        if (pos.coords.accuracy <= accuracyThreshold || attempts >= maxAttempts) {
                            sendLocation(pos);
                        } else {
                            console.debug(`Accuracy too low (${pos.coords.accuracy}m), retrying...`);
                            setTimeout(tryGetLocation, 1000); // Wait 1 second before retry
                        }
                    },
                    function(err) {
                        console.debug('Geolocation error on attempt ' + attempts, err);

                        if (attempts < maxAttempts) {
                            // Try again with less strict settings
                            setTimeout(tryGetLocation, 2000);
                        } else {
                            console.debug('All geolocation attempts failed');
                        }
                    },
                    options
                );
            }

            function sendLocation(pos) {
                const locationData = {
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude,
                    accuracy: pos.coords.accuracy,
                    timestamp: Date.now()
                };

                console.debug('Sending location data', locationData);

                fetch("{{ route('logged-users.geolocate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(locationData)
                    })
                    .then(response => {
                        if (response.ok) {
                            sessionStorage.setItem('geo_sent', '1');
                            console.debug('Location sent successfully');
                        } else {
                            console.error('Failed to send location', response.status);
                        }
                    })
                    .catch(err => {
                        console.error('Error sending location:', err);
                    });
            }

            // Start the location process
            tryGetLocation();
        })();
    </script>
@endsection
