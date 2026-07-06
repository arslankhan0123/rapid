<div id="payModal" class="modal fade address-modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Salary</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <form id="payForm">
                    <!-- Hidden input to hold salary sheet ID -->
                    <input type="hidden" name="salary_sheet_id" id="salaryId">

                    <!-- Select payment method -->
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select id="paymentMethod" name="payment_type" class="form-control">
                            <option value="cash"
                                {{ $salarySheet->salaryPayment?->payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank"
                                {{ $salarySheet->salaryPayment?->payment_type == 'bank' ? 'selected' : '' }}>Bank</option>
                        </select>
                    </div>

                    <!-- Bank options (hidden by default) -->
                    <div class="form-group {{ $salarySheet->salaryPayment?->payment_type == 'bank' ? ' ' : 'd-none' }} "
                        id="bankOptions">
                        <label for="bankSelect">Select Bank</label>
                        {{ Form::select('bank_id', $banks, $salarySheet->salaryPayment?->bank_id ?? null, ['class' => 'form-control', 'required', 'id' => 'bankSelect']) }}
                    </div>
                    <!-- Input for amount -->
                    <div class="form-group">
                        <label for="amount">Amount <span class="required">*</span></label>
                        <input type="number" step="any" id="amount" name="amount"
                            value="{{ $salarySheet->salaryPayment?->amount ?? '0.00' }}" class="form-control" required>
                    </div>
                    <!-- Modal footer with buttons -->
                    <div class="text-right">

                        <button type="submit" class="btn btn-primary" style="line-height: 30px;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
