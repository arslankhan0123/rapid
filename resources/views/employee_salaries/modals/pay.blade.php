<div id="listPayModal" class="modal fade address-modal" role="dialog">
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
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>

                    <!-- Bank options (hidden by default) -->
                    <div class="form-group d-none" id="bankOptions">
                        <label for="bankSelect">Select Bank</label>
                        <select id="bankSelect" name="bank_id" class="form-control">
                            <option value="">Select Bank</option>
                        </select>
                    </div>

                    <!-- Input for amount -->
                    <div class="form-group">
                        <label for="amount">Amount <span class="required">*</span></label>
                        <input type="number" step="any" id="amount" name="amount" value=""
                            class="form-control" required>
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
