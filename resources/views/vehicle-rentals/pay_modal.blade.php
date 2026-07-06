<!-- Pay Modal -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog " style="margin-top: 8%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Make Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="payForm">
                    <input type="hidden" name="id">
                    <input type="hidden" name="rental_id">

                    <div class="form-group col-md-12">
                        {{ Form::label('branch', 'Branch') }} <span class="required">*</span>
                        {{ Form::select('branch_id', $usersBranches, null, ['class' => 'form-control', 'required', 'id' => 'branch','placeholder'=>"Select Branch"]) }}
                    </div>

                    <div class="form-group col-md-12">
                        {{ Form::label('cash', 'Cash') }} <span class="required">*</span>
                        {{ Form::select('account_id', $accounts->pluck('account_name','id'),null, ['class' => 'form-control', 'required', 'id' => 'selectAcount']) }}
                    </div>

                    <div class="form-group col-md-12">
                        {{ Form::label('amount', 'Amount') }} <span class="required">*</span>
                        {{ Form::number('paid_amount', null, ['class' => 'form-control', 'required', 'id' => 'amount', 'min' => '1']) }}
                    </div>

                    <div class="row justify-content-end mr-1">
                        <button type="submit" class="btn btn-warning">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
