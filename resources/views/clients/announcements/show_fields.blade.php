<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('subject',  __('messages.announcement.subject').':') }}
            <p>{{ html_entity_decode($announcement->subject) }}</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('date',__('messages.announcement.announcement_date').':') }}
            <p>{{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('jS M, Y H:i A') }}</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('created_at', __('messages.common.created_on').':') }}<br>
            <span data-toggle="tooltip" data-placement="right"
                  title="{{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('jS M, Y') }}">{{ $announcement->created_at->diffForHumans() }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('updated_at', __('messages.common.last_updated').':') }}
            <br>
            <span data-toggle="tooltip" data-placement="right"
                  title="{{ \Carbon\Carbon::parse($announcement->updated_at)->translatedFormat('jS M, Y') }}">{{ $announcement->updated_at->diffForHumans() }}</span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('description', __('messages.announcement.message').':') }}
            <br>{!! !empty($announcement->message) ? ($announcement->message) : __('messages.common.n/a')!!}
        </div>
    </div>
</div>

