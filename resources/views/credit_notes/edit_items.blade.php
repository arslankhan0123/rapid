<div class="col-lg-12  mt-2">
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

                <!-- Loop through salesItems and display existing data for editing -->
                @foreach ($creditNote->salesItems as $index => $item)
                    <tr data-index="{{ $index }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="p-1" style="width: 13%;">
                            <input type="text" class="form-control item-number"
                                name="itemsArr[{{ $index }}][item]" value="{{ $item->item }}" readonly>
                        </td>
                        <td class="p-1 m-0" style="width:25%;">
                            <select class="form-select categorySelect select2" style="width:100%;"
                                name="itemsArr[{{ $index }}]['category_id']" required>
                                <option value="" disabled selected>Select a Category</option>
                                @foreach ($categories as $id => $name)
                                    <option value="{{ $id }}"
                                        @if ($id == $item->category_id ?? -1) selected @endif>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        {{-- {{  dd($invoice->project_id)}} --}}
                        <td class="p-1 m-0" style="width:25%;">
                            <select class="form-select serviceSelect" style="width:100%;"
                                name="itemsArr[{{ $index }}][service_id]" required>
                                <option value="" disabled selected>Select a service</option>

                                @foreach ($creditNote->invoice->project['services'] as $projectService)
                                    @php
                                        // Find the service from the $services list by matching the service_id
                                        $matchingService = $services->firstWhere('id', $projectService['service_id']);
                                    @endphp

                                    @if ($matchingService && $matchingService->item_group_id == $item['category_id'])
                                        <option value="{{ $matchingService->id }}"
                                            data-item-number="{{ $matchingService['item_number'] }}"
                                            @if ($matchingService->id == $item->service_id) selected @endif>
                                            {{ $matchingService['title'] }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                        </td>

                        <td class="p-1 m-0">
                            <input type="number" class="form-control text-right quantity" required
                                value="{{ $item->quantity }}" name="itemsArr[{{ $index }}][quantity]">
                        </td>
                        <td class="p-1 m-0">
                            <input type="number" class="form-control text-right rates" required
                                value="{{ $item->rate }}" name="itemsArr[{{ $index }}][rate]"
                                style="width: 100px !important;" readonly>
                        </td>

                        <td class="p-0">
                            <input type="number" class="form-control text-right discount"
                                value="{{ $item->discount }}" name="itemsArr[{{ $index }}][discount]"
                                style="width:90px;background:white;border:none;">
                        </td>
                        <td class="p-1 m-0" style="width: 10%;">
                            <input type="number" class="form-control text-right taxable" value="{{ $item->taxable }}"
                                readonly style="background:white;border:none;" readonly>
                        </td>
                        <td class="p-0">
                            <input type="number" class="form-control text-right tax" required value="15.00"
                                name="itemsArr[{{ $index }}][tax]" readonly
                                style="width:90px;background:white;border:none;">
                        </td>
                        <td class="vat-amount">{{ $item->vat_amount }}</td>

                        <td class="p-0">
                            <input type="number" class="total-amount form-control text-right"
                                name="itemsArr[{{ $index }}][total]" value="{{ $item->total }}"
                                style="background:white;border:none;width:90px;" readonly>
                        </td>
                        <td class="p-1 text-right">
                            <button type="button" class="btn text-danger remove-row ">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>

                    </tr>
                @endforeach

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
                        <option value="1" {{ $creditNote->discount_type == 1 ? 'selected' : '' }}>$
                        </option>
                        <option value="0" {{ $creditNote->discount_type == 0 ? 'selected' : '' }}>%
                        </option>
                    </select>
                </div>
                <div class="col pl-1">
                    <div class="input-group" style="width: 100%;">
                        <select name="percentage_discount" id="percentage_discount"
                            style="display: {{ $creditNote->discount_type == 1 ? 'none' : '' }};width:30%;"
                            class="form-control p-0 text-center">
                            @for ($i = 1; $i < 100; $i++)
                                <option value="{{ $i }}"
                                    {{ $creditNote->discount_type == 0 ? ($creditNote->percentage_discount == $i ? 'Selected' : '') : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        <input type="number" name="final_discount" id="totalDiscount"
                            value="{{ $creditNote->discount ?? 0 }}" class="form-control text-right"
                            style="width:70%;">
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
                <input type="number" name="adjustment" id="adjustment" value="0"
                    class="form-control text-right" readonly>
            </div>
        @endif


        <!-- Net Amount -->
        <div class="col-md-2">
            <label for="netTotal" class="form-label text-dark">Net Amount</label>
            <input type="text" id="netTotal" value="0.00" class="form-control text-right" readonly>
        </div>

        <input type="hidden" name="total_amount_new" id="totalAmt">
    </div>
    <!-- Totals Section (Similar to the "add" form) -->
    <div class="row justify-content-between mt-3">
        <div class="col-md-12 p-0 m-0">
            {{-- <div class="col-md-6 p-0 m-0">
                <label for="terms_dropdown">Select Terms & Conditions:</label>
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
                <table class="table table-bordered" id="terms_table">

                    <tbody>
                        <!-- Existing terms will be dynamically populated here on page load -->
                        @foreach ($creditNote->invoice?->project?->terms as $index => $estimateTerm)
                            <tr data-id="{{ $estimateTerm['terms_id'] }}">
                                <td style="width: 50px;">{{ $index + 1 }}</td>
                                <td class="p-0 m-0">
                                    <textarea readonly name="description[]" class="form-control" style="height:120px;width:100%;background:white;">{{ $estimateTerm['description'] }}</textarea>
                                    <input type="hidden" name="terms[]" value="{{ $estimateTerm['terms_id'] }}">
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
