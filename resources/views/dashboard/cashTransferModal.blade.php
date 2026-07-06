<div class="modal fade" role="dialog" id="cashTransferModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cash Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'cashTransferForm']) }}
            <div class="modal-body">

                <div class="row">
                    <!-- Transfer ID Field -->
                    <div class="form-group  col-md-12">
                        {{ Form::label('transfer_id', 'Branch') }}<span class="required">*</span><br>
                        {{ Form::select('branch_id', $usersBranches ?? [], null, ['class' => 'form-control', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                    </div>


                    <!-- From Account Field -->
                    <div class="form-group col-sm-12">
                        {{ Form::label('from_account', 'Transfer From') }}<span class="required">*</span>
                        {{ Form::select('from_account', $accounts->pluck('account_name', 'id') ?? [], null, [
                            'class' => 'form-control',
                            'required',
                            'id' => 'from_account',
                            'style' => 'pointer-events: none;  background-color: #e9ecef;',
                        ]) }}
                    </div>

                    <!-- To Account Field -->
                    <div class="form-group col-sm-12">
                        {{ Form::label('to_account', 'Transfer To') }}<span class="required">*</span>
                        {{ Form::select('to_account', $accounts->pluck('account_name', 'id') ?? [], null, ['class' => 'form-control', 'required', 'id' => 'to_account', 'style' => 'pointer-events: none;  background-color: #e9ecef;']) }}
                    </div>

                    <!-- Transfer Amount Field -->
                    <div class="form-group col-sm-12 mb-3">
                        {{ Form::label('transfer_amount', 'Amount') }}<span class="required">*</span>
                        {{ Form::number('transfer_amount', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'transfer_amount', 'autocomplete' => 'off', 'step' => 'any']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnEditSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...", 'style' => 'line-height:30px;']) }}

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>



<div class="modal fade" role="dialog" id="payAccountModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'payAccountForm']) }}
            <div class="modal-body">
                {{ Form::hidden('account_id', null, ['id' => 'account_id']) }}
                {{ Form::hidden('branch_id', null, ['id' => 'branch_id']) }}
                <div class="row">
                    <!-- Transfer ID Field -->
                    <!-- Transfer Amount Field -->
                     <div class="form-group col-sm-12 mb-3">
                        {{ Form::label('transfer_amount', 'Date') }}
                        {{ Form::date('date', null, ['class' => 'form-control','id' => 'input_date' ]) }}
                    </div>
                    <div class="form-group col-sm-12 mb-3">
                        {{ Form::label('transfer_amount', 'Amount') }}<span class="required">*</span>
                        {{ Form::number('amount', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'pay_amount', 'autocomplete' => 'off', 'step' => 'any']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnpayAccount', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...", 'style' => 'line-height:30px;']) }}

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>





<div class="modal fade" role="dialog" id="updatePayAccountModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Cash</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'payAccountFormUpdate']) }}
            <div class="modal-body">
                {{ Form::hidden('account_id', null, ['id' => 'account_id']) }}
                {{ Form::hidden('branch_id', null, ['id' => 'branch_id']) }}
                <div class="row">
                    <!-- Transfer ID Field -->
                    <!-- Transfer Amount Field -->
                     <div class="form-group col-sm-12 mb-3">
                        {{ Form::label('transfer_amount', 'Date') }}
                        {{ Form::date('date', null, ['class' => 'form-control','id' => 'update_input_date' ]) }}
                    </div>
                    <div class="form-group col-sm-12 mb-3">
                        {{ Form::label('transfer_amount', 'Amount') }}<span class="required">*</span>
                        {{ Form::number('amount', null, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'update_pay_amount', 'autocomplete' => 'off', 'step' => 'any']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnupdatePayAccount', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...", 'style' => 'line-height:30px;']) }}

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
