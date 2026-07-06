<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('date', __('messages.safety_materials.date')) !!}
            <span class="text-danger">*</span>
            {!! Form::date('date', optional($safetyMaterial ?? null)->date?->format('Y-m-d'), [
                'class' => 'form-control',
                'required',
                'id' => 'date',
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('employee_id', __('messages.safety_materials.employee')) !!}
            <span class="text-danger">*</span>
            {!! Form::select('employee_id', $employees, optional($safetyMaterial ?? null)->employee_id, [
                'class' => 'form-control select2',
                'required',
                'id' => 'employee_id',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('category', __('messages.safety_materials.category')) !!}
            <span class="text-danger">*</span>
            {!! Form::text('category', optional($safetyMaterial ?? null)->category, [
                'class' => 'form-control',
                'required',
                'id' => 'category',
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('amount', __('messages.safety_materials.amount')) !!}
            <span class="text-danger">*</span>
            {!! Form::number('amount', optional($safetyMaterial ?? null)->amount, [
                'class' => 'form-control',
                'step' => '0.01',
                'min' => '0',
                'required',
                'id' => 'amount',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('duration', __('messages.safety_materials.duration')) !!}
            <span class="text-danger">*</span>
            {!! Form::select('duration', $durationOptions, optional($safetyMaterial ?? null)->duration, [
                'class' => 'form-control select2',
                'required',
                'id' => 'duration',
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('next_date', __('messages.safety_materials.next_date')) !!}
            <span class="text-danger">*</span>
            {!! Form::date('next_date', optional($safetyMaterial ?? null)->next_date?->format('Y-m-d'), [
                'class' => 'form-control',
                'readonly',
                'id' => 'next_date',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('note', __('messages.safety_materials.note')) !!}
            {!! Form::textarea('note', optional($safetyMaterial ?? null)->note, [
                'class' => 'form-control',
                'rows' => 3,
                'id' => 'note',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-right">
        {{ Form::button(__('messages.common.submit'), [
            'type' => 'submit',
            'class' => 'btn btn-primary btn-lg px-4',
            'id' => 'btnSave',
            'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...",
        ]) }}
    </div>
</div>
