<div class="col-lg-12">

    <div class="row">

        <div class="col-md-6 col-sm-12 p-0 pb-3">
            <label for=""><strong>Select Item</strong></label><br>
            <select id="serviceSelect" class="form-select select2" style="width: 100%;">
                <option value="" disabled selected>Select a Item</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" data-name="{{ $service->name }}">
                        {{ $service->name }}
                    </option>
                @endforeach
            </select>
        </div>



        <table class="table table-responsive w-100" id="itemsTable" >
            <thead>
                <tr>
                    <th style="width: 3%;">Sl</th>
                    <th style="width: 12%;">Item<br>Code</th>
                    <th style="width: 35%;">Item<br>Name</th>
                    <th style="width: 7%;">Unit<br>Qty<span class="required">*</span></th>
                    <th style="width: 12%;">Unit<br>Rate</th>
                    <th style="width: 12%;">Excluding<br>VAT</th>
                    <th style="width: 10%;">VAT<br>%</th>
                    <th style="width: 10%;">VAT<br>Amount</th>
                    <th  style="width: 12%;">Including<br>VAT</th>
                    <th class="p-0" >Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">
                <!-- Rows will be added dynamically here -->
            </tbody>
        </table>


    </div>





    <!-- Totals Table -->
    <div class="row justify-content-between mt-5">

        <div class="col-md-6 p-0 m-0">
            <div class="col-md-6 p-0 m-0">
                <label for="terms_dropdown">Select Terms & Conditions:</label>
                <select class="form-control" id="terms_dropdown">
                    <option value=""></option>
                    @foreach ($terms as $key => $term)
                        <option value="{{ $key }}">{{ strip_tags($term) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-3">
                <table class="table table-bordered" id="terms_table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">SL</th>
                            <th>Description</th>
                            <th style="width: 20px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Terms will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </div>



        <div class="col-md-6 col-lg-5 col-xl-5 p-0 m-0 mt-4">
            <table class="table table-bordered" style="width: 100%;">
                <tbody>
                    <tr>
                        <th style="text-align: right">Total Before Discount</th>
                        <td class="text-right" style="width: 250px;" id="includingVat">0.00 SAR</td>
                    </tr>

                    <tr style="text-align: right">
                        <th>
                            <div class="row float-right">
                                <div class="col" style="line-height: 30px;">Discount </div>
                                <div class="col">
                                    <select name="discount_type" id="discount_type" class="form-control p-0"
                                        style="width: 60px;">
                                        <option value="1">$</option>
                                        <option value="0">%</option>

                                    </select>
                                </div>
                            </div>
                        </th>

                        <td class="text-right"><input type="number" style="max-width: 200px;float:right;"
                                name="final_discount" id="totalDiscount" value="0" class="form-control text-right">
                        </td>
                    </tr>
                    {{-- <tr>
                        <th style="text-align: right">Subtotal After Discount</th>
                        <td class="text-right" id="totalVAT">0.00 SAR</td>
                    </tr> --}}

                    <tr style="text-align: right">
                        <th>Total After Discount</th>
                        <td class="text-right" id="afterDiscount">0.00 SAR</td>
                    </tr>
                    <tr style="text-align: right">
                        <th>Total VAT</th>
                        <td class="text-right" id="totalVAT">0.00 SAR</td>
                    </tr>


                    <tr>
                        <th style="text-align: right">Total Net</th>
                        <td class="text-right" id="netTotal">0.00 SAR</td>
                        <input type="hidden" name="total_amount" id="total_amount">
                        <input type="hidden" name="sub_total" id="sub_total">
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
