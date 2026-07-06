<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title ml-2">{{ __('messages.attendances.update') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                {{ Form::open(['id' => 'updateAttendanceForm']) }}
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="date">{{ __('messages.attendances.date') }}</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="time_in">{{ __('messages.attendances.time_in') }}</label>
                            {{ Form::time('time_in', null, ['class' => 'form-control', 'id' => 'time_in_edit', 'required']) }}

                        </div>
                        <div class="form-group col-sm-12">
                            <label for="time_out col-sm-12">{{ __('messages.attendances.time_out') }}</label>
                            <input type="time" class="form-control" id="time_out_edit" name="time_out" required>
                        </div>
                        <input type="hidden" id="editId" name="id">

                    </div>
                    <div class="text-right">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                        <button type="button" id="btnCancel" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
