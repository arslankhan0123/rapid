<div class="col-lg-12">




    <div class="row pb-1">
        <div class="col-md-4 p-0">
            <label for=""><strong>Items</strong></label>
            <input type="text" id="serviceSearch" class="form-control" placeholder="Type to search for Items">
        </div>

    </div>
    <div class="row page_contents ">

        <table class="table table-responsive table-md" id="itemsTable" style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 5%;">Sl</th>
                    <th style="width: 8%;">Item Code</th>
                    <th style="width: 35%;">Item Name <span class="required">*</span></th>
                    <th style="width: 7%;" class="text-center">Unit<br>Qty <span class="required">*</span></th>
                    <th style="width: 8%;" class="text-center">Unit<br>Rate</th>
                    <th style="width: 10%;" class="text-center">Excluding<br>VAT</th>
                    <th style="width: 5%;" class="text-center">VAT<br>%</th>
                    <th style="width: 6%;" class="text-center">VAT<br>Amount</th>
                    <th style="width: 10%;" class="text-center">Including<br>VAT</th>
                    <th class="p-1" style="width: 2%;">Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">
                @foreach ($estimate->salesItems as $index => $item)
                    <tr data-index="{{ $index }}">
                        <td style="width: 2%;">{{ $index + 1 }}</td>
                        <td>
                            {{ $item->service->code ?? '' }}
                            <input type="hidden" name="itemsArr[{{ $index }}][service_id]"
                                value="{{ $item->service_id }}">
                            <input type="hidden" name="itemsArr[{{ $index }}][description]"
                                value="{{ $item->description }}">
                        </td>
                        <td style="width: 20%;">{{ $item->description }}</td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right quantity" required
                                value="{{ $item->quantity }}" name="itemsArr[{{ $index }}][quantity]">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right rates" required
                                value="{{ $item->rate }}" name="itemsArr[{{ $index }}][rate]">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right taxable"
                                value="{{ $item->taxable }}" name="itemsArr[{{ $index }}][taxable]" readonly>
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control smallInput text-right tax" value="15"
                                name="itemsArr[{{ $index }}][tax]" readonly>
                        </td>
                        <td class="vat-amount text-center">{{ $item->vat_amount }}</td>
                        <td class="p-1">
                            <input type="number" class="total-amount smallInput form-control text-right"
                                name="itemsArr[{{ $index }}][total]" value="{{ $item->total }}" readonly>
                        </td>
                        <td class="p-1 text-right">
                            <button type="button" class="btn text-danger remove-row">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>


    <!-- Totals Section (Similar to the "add" form) -->
    <div class="row justify-content-between mt-3 page_footer">
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
                        <!-- Existing terms will be dynamically populated here on page load -->
                        @foreach ($estimate->terms as $index => $estimateTerm)
                            <tr data-id="{{ $estimateTerm['terms_id'] }}">
                                <td>{{ $index + 1 }}</td>
                                <td class="p-0 m-0">
                                    <textarea name="description[]" class="form-control" style="height:120px;width:100%;">{{ $estimateTerm['description'] }}</textarea>
                                    <input type="hidden" name="terms[]" value="{{ $estimateTerm['terms_id'] }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm delete-row"><i
                                            class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
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
                                        <option value="1" {{ $estimate->discount_type == 1 ? 'selected' : '' }}>$
                                        </option>
                                        <option value="0" {{ $estimate->discount_type == 0 ? 'selected' : '' }}>%
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </th>
                        <td class="text-right"><input type="number" style="max-width: 200px;float:right;"
                                name="final_discount" id="totalDiscount" value="{{ $estimate->discount ?? 0 }}"
                                class="form-control text-right">
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
