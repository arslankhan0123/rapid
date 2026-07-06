<!-- Preview Salary Sheet Modal -->
<div class="modal fade" id="previewSalarySheet" tabindex="-1" aria-labelledby="previewSalarySheetLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90% !important;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="previewSalarySheetLabel"><strong>
                        </strong> <span id="salaryMonth" style="text-warning"></span> Salary Information </h5>
                <button type="button" aria-label="Close" class="close text-white" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <!-- Salary Info -->
                <input type="hidden" id="salaryGenerateId">
                <div id="salaryInfo" class="mb-4">

                </div>

                <!-- Employee Info -->
                <div id="employeeInfo">
                    <h6>Employees Salary Details</h6>
                    <div id="employeeList"></div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" id="btnCancel" class="btn btn-light ml-1"
                    data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                <button type="button" id="btnApprove" class="btn btn-danger">
                    Approve
                </button>
            </div>
        </div>
    </div>
</div>
