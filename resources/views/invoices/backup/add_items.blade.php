<div class="col-lg-12">

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
        <!--<button type="button" class="btn btn-success ml-2" id="addItem"><i class="fa fa-plus"></i> Add Item</button>-->
    </div>

    <div class="d-flex align-items-end pt-1 flex-nowrap" style="overflow-x:auto; gap:8px;">

        <!-- Subtotal -->
        <div style="width: 130px;">
            <label for="subtotal" class="form-label text-dark mb-1">Subtotal</label>
            <input class="form-control text-right" id="subtotal" value="0.00" readonly>
        </div>

        <!-- Absent Deduction -->
        <div style="width: 150px;">
            <label for="absentDeduction" class="form-label text-dark mb-1">Absent Deduction</label>
            <input type="number" name="absent_deduction" id="absentDeduction" value="0.00"
                class="form-control text-right deduction-field">
        </div>

        <!-- Allowance Deduction -->
        <div style="width: 160px;">
            <label for="allowanceDeduction" class="form-label text-dark mb-1">Allowance Deduction</label>
            <input type="number" name="allowance_deduction" id="allowanceDeduction" value="0.00"
                class="form-control text-right deduction-field">
        </div>

        <!-- Damage Deduction -->
        <div style="width: 150px;">
            <label for="damageDeduction" class="form-label text-dark mb-1">Damage Deduction</label>
            <input type="number" name="damage_deduction" id="damageDeduction" value="0.00"
                class="form-control text-right deduction-field">
        </div>

        <!-- Total Discount -->
        <div style="width: 220px;">
            <label for="totalDiscount" class="form-label text-dark mb-1">Total Discount</label>
            <div class="d-flex">
                <select name="discount_type" id="discount_type" class="form-control text-center me-1"
                    style="width: 55px; font-weight:bold;">
                    <option value="1">$</option>
                    <option value="0">%</option>
                </select>
                <select name="percentage_discount" id="percentage_discount" class="form-control text-center me-1"
                    style="display:none; width:80px;">
                    @for ($i = 1; $i < 100; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <input type="number" name="final_discount" id="totalDiscount" value="0.00"
                    class="form-control text-right">
            </div>
        </div>

        <!-- Total Taxable -->
        <div style="width: 130px;">
            <label for="taxable" class="form-label text-dark mb-1">Total Taxable</label>
            <input type="text" id="taxable" value="0.00" class="form-control text-right" readonly>
        </div>

        <!-- Total VAT -->
        <div style="width: 110px;">
            <label for="totalVAT" class="form-label text-dark mb-1">Total VAT</label>
            <input type="text" id="totalVAT" value="0.00" class="form-control text-right" readonly>
        </div>

        <!-- Round Off (conditional) -->
        @if ($isRound)
            <div style="width: 120px;">
                <label for="adjustment" class="form-label text-dark mb-1">Round Off</label>
                <input type="number" name="adjustment" id="adjustment" value="0"
                    class="form-control text-right" readonly>
            </div>
        @endif

        <!-- Net Amount -->
        <div style="width: 140px;">
            <label for="netTotal" class="form-label text-dark mb-1">Net Amount</label>
            <input type="text" id="netTotal" value="0.00" class="form-control text-right" readonly>
        </div>

        <input type="hidden" name="total_amount_new" id="totalAmt">
    </div>
    <!-- Totals Table -->
    <div class="row justify-content-between mt-2">

        <div class="col-md-12 p-0 m-0">
            {{-- <div class="col-md-6 p-0 m-0">

                <select class="form-control" id="terms_dropdown">
                    <option value=""></option>
                    @foreach ($terms as $key => $term)
                        <option value="{{ $key }}">{{ strip_tags($term) }}</option>
                    @endforeach
                </select>
            </div> --}}

            <div style="width: 100%;background:lightblue;color:black;height:40px;border-radius:5px;">
                <label for="terms_dropdown" style="font-weight: bold;line-height:40px;padding-left:10px;"> Terms &
                    Conditions</label>
            </div>


            <div class="mt-3" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered p-0 m-0" id="terms_table">
                    <tbody>
                        <!-- Terms will be dynamically added here -->
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
