@extends('members.show')
@section('section')
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('name', __('messages.common.name') ) }}
                <p>{{ html_entity_decode($member->full_name) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('phone', __('messages.member.phone') ) }}
                <p>{{ isset($member->phone) ? $member->phone : __('messages.common.n/a') }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('email', __('messages.member.email') ) }}
                <p>{{ $member->email }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('is_enable', __('messages.common.status') ) }}
                <p>{{ isset($member->is_enable) && $member->is_enable ? __('messages.contact.active') : __('messages.contact.deactive') }}
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('staff_member', __('messages.member.staff_member') ) }}
                <p>{{ $member->staff_member ? __('messages.common.yes') : __('messages.common.no') }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('send_welcome_email', __('messages.member.send_welcome_email') ) }}
                <p>{{ $member->send_welcome_email ? __('messages.common.yes') : __('messages.common.no') }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('facebook', __('messages.member.facebook') ) }}
                <p>{{ isset($member->facebook) ? $member->facebook : __('messages.common.n/a') }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('skype', __('messages.member.skype') ) }}
                <p>{{ isset($member->skype) ? $member->skype : __('messages.common.n/a') }}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('linkedin', __('messages.member.linkedin') ) }}
                <p>{{ isset($member->linkedin) ? $member->linkedin : __('messages.common.n/a') }}</p>
            </div>
        </div>



        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('created_at', __('messages.common.created_on') ) }}<br>
                <span data-toggle="tooltip" data-placement="right"
                    title="{{ \Carbon\Carbon::parse($member->created_at)->translatedFormat('jS M, Y') }}">{{ $member->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('updated_at', __('messages.common.last_updated') ) }}
                <br>
                <span data-toggle="tooltip" data-placement="right"
                    title="{{ \Carbon\Carbon::parse($member->created_at)->translatedFormat('jS M, Y') }}">{{ $member->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{ Form::label('linkedin', __('messages.branches.branches') ) }}
            @if ($member->branches->isNotEmpty())
                <!-- Check if branches exist -->
                <div class="row">
                    <div class="col-md-12 mb-1">
                        <div>
                            @foreach ($member->branches as $branch)
                                @if (isset($branch['branch']))
                                    <span class="text-primary font-weight-bold">{{ $branch['branch']['name'] }}</span>
                                    @if (!$loop->last)
                                        <span class="mx-2">,</span>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- Handle case where no branches are found -->
                <p>No branches assigned to this member.</p>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            {{ Form::label('permissions', __('messages.member.permissions') , ['class' => 'section-title']) }}
        </div>
        @foreach ($memberPermissions as $type => $permissions)
            <div class="col-md-6 col-lg-4 col-xl-3 col-sm-4 permission-text">
                <div class="card-body">
                    <div class="section-title mt-0">{{ $type }}</div>
                    @foreach ($permissions as $permission)
                        <div>
                            <label>
                                {{ $permission['display_name'] }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
