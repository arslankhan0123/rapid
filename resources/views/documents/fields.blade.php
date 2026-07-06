@if (auth()->id() === 1)
    {{-- <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('user_id', __('messages.documents.user') . ':') !!} <span class="text-danger">*</span>
                {!! Form::select('user_id', ['' => 'Select User'] + $users->toArray(), null, [
                    'class' => 'form-control select2',
                    'required',
                ]) !!}
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('user_id', __('messages.documents.user') . ':') !!} <span class="text-danger">*</span>
                @if (auth()->id() === 1)
                    {!! Form::select('user_id[]', $users, null, [
                        'class' => 'form-control select2',
                        'multiple' => 'multiple',
                        'required',
                        'id' => 'user_id',
                    ]) !!}
                @else
                    {!! Form::select('user_id[]', [auth()->id() => auth()->user()->name], auth()->id(), [
                        'class' => 'form-control',
                        'readonly' => 'readonly',
                        'disabled' => 'disabled',
                    ]) !!}
                    {!! Form::hidden('user_id[]', auth()->id()) !!}
                @endif
            </div>
        </div>
    </div>
@else
    {!! Form::hidden('user_id', auth()->id()) !!}
@endif

<div id="documentContainer" class="mb-3">
    <div class="form-group row document-row">
        <div class="col-md-5">
            {!! Form::label('document[]', __('messages.documents.document') . ':') !!} <span class="text-danger">*</span>
            {!! Form::file('document[]', [
                'class' => 'form-control',
                'required',
                'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx,.csv',
            ]) !!}
            <small class="form-text text-muted">
                {{ __('messages.documents.allowed_types') }}: PDF, Word, Docx, Doc, xls, JPG, PNG, Excel, CSV. <br>
                {{ __('messages.documents.max_size') }}: 10MB
            </small>
        </div>


        <div class="col-md-5">
            {!! Form::label('description[]', __('messages.documents.description') . ':') !!}
            {!! Form::textarea('description[]', null, [
                'class' => 'form-control',
                'rows' => 2,
            ]) !!}
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger removeDocument" style="display:none;">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <button type="button" id="addDocument" class="btn btn-primary">
            <i class="fa fa-plus"></i>
        </button>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-right"> {!! Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary']) !!} </div>
</div>
