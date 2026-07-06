{{-- <!-- Modal Structure -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Report Information</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"> <i
                        class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <!-- Dynamic content will be injected here -->
                <div>
                    {{ Form::open(['id' => 'report-form']) }}

                    <!-- Period Label and Input -->
                    <div class="mb-3">
                        <label for="period" class="form-label">Period</label>
                        <input type="text" class="form-control" id="period" name="period" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="period" class="form-label">Branch</label>
                        <input type="text" class="form-control" id="branch" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="paid" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" name="bank_name" id="bank_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="paid" class="form-label">Account Number</label>
                        <input type="text" class="form-control" name="account_number" required id="account_number">
                    </div>
                    <!-- Paid Field Label and Input -->
                    <div class="mb-3">
                        <label for="paid" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="paid" name="paid" required>
                    </div>

                    <input type="hidden" name="id" id="vat_report_id">
                    {{ Form::close() }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="report-form" class="btn btn-success" id="submit-report">Submit</button>
            </div>
        </div>
    </div>
</div> --}}


<!-- Modal Structure -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">VAT Payment - All Branches (Aggregated)</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> This payment will be recorded for <strong>ALL BRANCHES</strong>
                    combined.
                </div>
                <div>
                    {{ Form::open(['id' => 'report-form']) }}

                    <!-- Period Label and Input -->
                    <div class="mb-3">
                        <label for="period" class="form-label">Period</label>
                        <input type="text" class="form-control" id="period" name="period" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="period" class="form-label">Year</label>
                        <input type="text" class="form-control" id="year" name="year" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="branch" class="form-label">Scope</label>
                        <input type="text" class="form-control bg-light" id="branch"
                            value="All Branches (Aggregated)" disabled readonly>
                        <small class="form-text text-muted">Payment applies to all branches combined</small>
                    </div>
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" name="bank_name" id="bank_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_number" class="form-label">Account Number</label>
                        <input type="text" class="form-control" name="account_number" required id="account_number">
                    </div>
                    <div class="mb-3">
                        <label for="paid" class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="paid" name="paid" required
                                min="0" step="0.01" placeholder="Enter payment amount">
                        </div>
                        <small class="form-text text-muted">Remaining unpaid amount: <span
                                id="remaining-unpaid">0.00</span></small>
                    </div>

                    <input type="hidden" name="id" id="vat_report_id">
                    {{ Form::close() }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="report-form" class="btn btn-success" id="submit-report">
                    <i class="fas fa-money-bill-wave"></i> Submit Payment
                </button>
            </div>
        </div>
    </div>
</div>
