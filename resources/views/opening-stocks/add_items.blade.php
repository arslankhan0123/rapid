<div class="col-lg-12 pt-2">

    <div class="row pb-2 ">
        <div class="col-md-5 p-0 pr-2 col-sm-12">
            <div class="input-group">
                <span class="input-group-text" style="height: 38px;"><i class="fa fa-barcode"></i></span>
                <input type="text" id="serviceSearch" class="form-control" placeholder="Item Name/Barcode/Itemcode">
                <a class="input-group-text btn" id="addItemBtn" style="height: 38px;"><i class="fa fa-plus"></i></a>
            </div>
        </div>
        <div class="col-md-2 p-0 col-sm-6">
            <div class="input-group">
                <span class="input-group-text" style="height: 38px;">QTY</span>
                <input type="text" class="form-control text-right" value="1" id="globalQTY">
            </div>
        </div>
        <div class="col-md-2 p-0 pl-1 col-sm-6">
            <div class="input-group">
                <span class="input-group-text" style="height: 38px;">Unit</span>
                {{ Form::select('unit', $units, null, ['id' => 'globalUnit', 'class' => 'form-control','placeholder'=>"select Unit"]) }}
            </div>
        </div>
        <div class="col-md-3 p-0 pl-1 col-sm-6">
            <div class="input-group">
                <span class="input-group-text" style="height: 38px;">Rate</span>
                <input type="text" class="form-control text-right" value="0" id="globalRate">
            </div>
        </div>
    </div>
    <div class="row  ">

        {{-- <table class="table table-responsive table-md" style="width: 100%;">


        </table> --}}
        <table class="table table-responsive table-md page_contents" id="itemsTable" style="width: 100%;">
            <thead>
                <tr class="p-0">
                    <th class="p-1 text-center" style="width: 2%;">Sl</th>
                    <th class="p-1" style="width: 8%;">Item Code</th>
                    <th class="p-1" style="width: 32%;">Item Name <span class="required">*</span></th>
                    <th class="p-1 text-center" style="width: 7%;">Qty <span class="required">*</span></th>
                    <th class="p-1 text-center" style="width: 5%;">Unit<br></th>
                    <th class="p-1 text-center" style="width: 8%;">Rate</th>
                    <th class="p-1 text-center" style="width: 5%;">Disc %<br></th>
                    <th class="p-1 text-center" style="width: 7%;">Disc Amt<br></th>
                    <th class="p-1 text-center" style="width: 9%;">Excluding<br>VAT</th>
                    <th class="p-1 text-center" style="width: 5%;">VAT<br>%</th>
                    <th class="p-1 text-center" style="width: 6%;">VAT<br>Amount</th>
                    <th class="p-1 text-center" style="width: 9%;">Including<br>VAT</th>
                    <th class="p-1 text-center" style="width: 2%;">Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">
                <!-- Initial rows will be added dynamically -->
            </tbody>
        </table>
    </div>


    <div class="row">
        <table class="table table-bordered table-sm pt-1 pb-1" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 20%;" class="d-none">
                        <strong>Total Before Discount:</strong>
                        <span id="includingVat" style="float: right;">0.00 SAR</span>
                    </td>

                    <td style="width: 20%;" class="d-none">
                        <strong>Discount:</strong>
                        <div class="d-inline-flex align-items-center" style="float: right;">
                            <select name="discount_type" id="discount_type" class="form-control d-inline-block"
                                style="width: 50px; height: 25px; padding: 0; margin-right: 5px;">
                                <option value="1">$</option>
                                <option value="0">%</option>
                            </select>
                            <input type="number" name="final_discount" id="totalDiscount" value="0"
                                class="form-control d-inline-block text-right"
                                style="width: 70px; height: 25px; padding: 0;">
                        </div>
                    </td>

                    <td style="width: 20%;" class="d-none">
                        <strong>Total After Discount:</strong>
                        <span id="afterDiscount" style="float: right;">0.00 SAR</span>
                    </td>

                    <td style="width: 20%;" class="d-none">
                        <strong>Total VAT:</strong>
                        <span id="totalVAT" style="float: right;">0.00 SAR</span>
                    </td>

                    <td style="width: 20%;">
                        <strong>Total Net:</strong>
                        <span id="netTotal" style="float: right;">0.00 SAR</span>
                        <input type="hidden" name="total_amount" id="total_amount">
                        <input type="hidden" name="sub_total" id="sub_total">
                    </td>
                </tr>
            </tbody>
        </table>


    </div>

</div>
