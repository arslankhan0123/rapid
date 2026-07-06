<div class="col-lg-12 mt-2">
    <div class="row">
        <table class="table table-responsive table-md page_contents" id="itemsTable">
            <thead>
                <tr style="padding: 0px;">
                    <th style="width: 5%;">S.</th>
                    <th class="text-center" style="width: 15%;">Item</th>
                    <th class="text-center" style="width: 15%;">Category</th>
                    <th class="text-center" style="width: 20%;">Description</th>
                    <th style="width: 5%;">Qty</th>
                    <th class="text-center pr-3" style="width: 10%;">Rate</th>
                    <th style="text-align: center; width: 5%;" class="pr-0">Disc.</th>
                    <th style="text-align: center; width: 5%;" class="pr-0 pr-2">Taxable</th>
                    <th style="text-align: center; width: 5%;" class="pr-0">Vat %</th>
                    <th style="text-align: center; width: 5%;" class="pr-0">Vat $</th>
                    <th class="text-center pr-0" style="width: 10%;">Net</th>
                    <th class="pr-0" style="width: 5%;">Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">
            </tbody>
        </table>
        <button type="button" class="btn btn-info" id="addRow"><i class="fa fa-plus"></i> Add Row</button>
    </div>

    <div class="row pt-1">
        <!-- Subtotal -->
        <div class="col-md-2 pl-0">
            <label for="subtotal" class="form-label text-dark">Subtotal</label>
            <input class="form-control pr-2 text-right " id="subtotal" value="0.00" readonly>
        </div>

        <!-- Total Discount -->
        <div class="col-md-3">
            <label for="totalDiscount" class="form-label text-dark">Total Discount</label>
            <div class="row">
                <div class="col-md-3 pr-0">
                    <select name="discount_type" id="discount_type" class="form-control p-0 text-center"
                        style="font-weight:bold;">
                        <option value="1">$</option>
                        <option value="0">%</option>
                    </select>
                </div>
                <div class="col pl-1">
                    <div class="input-group" style="width: 100%;">
                        <select name="percentage_discount" id="percentage_discount" style="display: none;width:30%;"
                            class="form-control p-0 text-center">
                            @for ($i = 1; $i < 100; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <input type="number" name="final_discount" id="totalDiscount" value="0.00"
                            class="form-control text-right" style="width:70%;">
                    </div>
                </div>
            </div>


        </div>

        <!-- Total Taxable -->
        <div class="col-md-2">
            <label for="taxable" class="form-label text-dark">Total Taxable</label>
            <input type="text" id="taxable" value="0.00" class="form-control text-right" readonly>
        </div>

        <!-- Total VAT -->
        <div class="col-md-1 p-0">
            <label for="totalVAT" class="form-label text-dark">Total Vat</label>
            <input type="text" id="totalVAT" value="0.00" class="form-control text-right" readonly>
        </div>

        @if ($isRound)
            <!-- Round Off -->
            <div class="col-md-2">
                <label for="adjustment" class="form-label text-dark">Round Off</label>
                <input type="number" name="adjustment" id="adjustment" value="0" class="form-control text-right"
                    readonly>
            </div>
        @endif


        <!-- Net Amount -->
        <div class="col-md-2">
            <label for="netTotal" class="form-label text-dark">Net Amount</label>
            <input type="text" id="netTotal" value="0.00" class="form-control text-right" readonly>
        </div>

        <input type="hidden" name="total_amount_new" id="totalAmt">
    </div>
    <!-- Totals Table -->
    <div class="row justify-content-between mt-5">


        <div class="col-md-12 p-0 m-0">

            <div style="width: 100%;background:lightblue;color:black;height:40px;border-radius:5px;">
                <label for="terms_dropdown" style="font-weight: bold;line-height:40px;padding-left:10px;"> Terms &
                    Conditions</label>
            </div>
            <div class="mt-3" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered" id="terms_table">

                    <tbody>
                        <!-- Terms will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
