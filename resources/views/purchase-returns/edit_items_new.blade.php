<div class="col-lg-12 pt-2">

    <div class="row pb-2 ">
        <div class="col-md-6 p-0 pr-2 col-sm-12">
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
        <div class="col-md-3 p-0 pl-1 col-sm-6">
            <div class="input-group">
                <span class="input-group-text" style="height: 38px;">Unit Rate</span>
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
                    <th class="p-1 text-center" style="width: 7%;">Unit<br>Qty <span class="required">*</span></th>
                    <th class="p-1 text-center" style="width: 5%;">Unit<br></th>
                    <th class="p-1 text-center" style="width: 8%;">Unit<br>Rate</th>
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
                @foreach ($estimate->salesItems->reverse()->values() as $index => $item)
                    @php
                        $displayIndex = $estimate->salesItems->count() - $index; // Calculate descending index
                    @endphp
                    <tr data-index="{{ $displayIndex - 1 }}"> <!-- Subtract 1 to make it 0-based for data-index -->
                        <td style="width: 2%;">{{ $displayIndex }}</td> <!-- Show descending index -->
                        <td>{{ $item->service->code ?? '' }}</td>
                        <td style="width: 32%;">
                            {{ $item['description'] }}
                            <input type="hidden" name="itemsArr[{{ $displayIndex - 1 }}][service_id]"
                                value="{{ $item['service_id'] }}">
                            <input type="hidden" name="itemsArr[{{ $displayIndex - 1 }}][description]"
                                value="{{ $item['description'] }}">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right quantity" required
                                value="{{ $item['quantity'] }}" name="itemsArr[{{ $displayIndex - 1 }}][quantity]">
                        </td>
                        <td class="p-1 text-center">{{ $item->service->unit->title ?? '' }}</td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right rates" required
                                value="{{ $item['rate'] }}" name="itemsArr[{{ $displayIndex - 1 }}][rate]">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right discount" required
                                value="{{ $item['discount'] }}" name="itemsArr[{{ $displayIndex - 1 }}][discount]">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right discountAmount"
                                value="{{ ($item['discount'] * $item['rate']) / 100 }}"
                                name="itemsArr[{{ $displayIndex - 1 }}][discountAmount]" readonly>
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right taxable"
                                value="{{ $item['taxable'] }}" name="itemsArr[{{ $displayIndex - 1 }}][taxable]"
                                readonly>
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right tax"
                                value="{{ $item['tax'] }}" name="itemsArr[{{ $displayIndex - 1 }}][tax]" readonly>
                        </td>
                        <td class="vat-amount text-center">{{ ($item['taxable'] * $item['tax']) / 100 }}</td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right total-amount"
                                value="{{ $item['total'] }}" name="itemsArr[{{ $displayIndex - 1 }}][total]"
                                readonly>
                        </td>
                        <td class="p-1 text-right">
                            <button type="button" class="btn text-danger remove-row"><i
                                    class="fa fa-times"></i></button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>


    <div class="row">
        <table class="table table-bordered table-sm pt-1 pb-1" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 20%;">
                        <strong>Total Before Discount:</strong>
                        <span id="includingVat" style="float: right;">0.00 SAR</span>
                    </td>

                    <td style="width: 20%;">
                        <strong>Discount:</strong>
                        <div class="d-inline-flex align-items-center" style="float: right;">
                            <select name="discount_type" id="discount_type" class="form-control d-inline-block"
                                style="width: 50px; height: 25px; padding: 0; margin-right: 5px;">
                                <option value="1" {{ $estimate->discount_type == 1 ? 'selected' : '' }}>$
                                </option>
                                <option value="0" {{ $estimate->discount_type == 0 ? 'selected' : '' }}>%
                                </option>
                            </select>
                            <input type="number" name="final_discount" id="totalDiscount" value="{{ $estimate->discount ?? 0 }}"
                                class="form-control d-inline-block text-right"
                                style="width: 70px; height: 25px; padding: 0;" >
                        </div>
                    </td>

                    <td style="width: 20%;">
                        <strong>Total After Discount:</strong>
                        <span id="afterDiscount" style="float: right;">0.00 SAR</span>
                    </td>

                    <td style="width: 20%;">
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
