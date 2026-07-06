<!-- Modal for PDF Preview -->
<div class="modal fade" id="pdfModals" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">PDF Preview</h5>
                <button type="button" class="btn btn-outline-primary ml-4 btn-sm" id="btnPrintLabel"
                    onclick="printPDF()">
                    <i class="fa fa-print"></i> Print
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Embed PDF into iframe -->
                <iframe id="pdfIframe" style="width: 100%; height: 500px; border: none;" class="d-none"></iframe>
                <div class="pdfHtml"
                    style="height: 350px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; width: 100%;">
                    <!-- HTML content or PDF preview will be dynamically inserted here -->
                </div>

            </div>
        </div>
    </div>
</div>
