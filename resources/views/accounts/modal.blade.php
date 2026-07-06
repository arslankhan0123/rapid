<!-- Modal -->
<div id="importModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Cash</h5>
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'importAttendances', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">


                <div class="form-group col-sm-12">
                    {{ Form::label('account_name', "Main Cash Name") }}<span
                        class="required">*</span>
                    {{ Form::text('account_name', 'Main Cash Name', ['class' => 'form-control', 'required', 'id' => 'account_name', 'autocomplete' => 'off','disabled']) }}
                </div>

                <div class="form-group col-sm-12">
                    {{ Form::label('account_name', "Main Cash Name") }}<span
                        class="required">*</span>
                    {{ Form::text('account_name', 'Main Cash Name', ['class' => 'form-control', 'required', 'id' => 'account_name', 'autocomplete' => 'off','disabled']) }}
                </div>
                <div class="form-group col-sm-12">
                    {{ Form::label('account_name', "Main Cash Name") }}<span
                        class="required">*</span>
                    {{ Form::text('account_name', 'Main Cash Name', ['class' => 'form-control', 'required', 'id' => 'account_name', 'autocomplete' => 'off','disabled']) }}
                </div>
                <div class="form-group col-sm-12">
                    {{ Form::label('account_name', "Main Cash Name") }}<span
                        class="required">*</span>
                    {{ Form::text('account_name', 'Main Cash Name', ['class' => 'form-control', 'required', 'id' => 'account_name', 'autocomplete' => 'off','disabled']) }}
                </div>


                <div class="form-group col-sm-12 ">
                    {{ Form::label('opening_balance', __('messages.accounts.opening_balance')) }}<span
                        class="required">*</span>
                    {{ Form::number('amount', 0, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'amount', 'autocomplete' => 'off']) }}
                </div>

                <div class="text-right">
                    <button type="button" id="btnCancel" class="btn btn-light ml-1 btnWarning text-white"
                        style="line-height:30px;" data-dismiss="modal">Cancel</button>
                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnImport', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...", 'style' => 'line-height:30px;']) }}

                </div>
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>
