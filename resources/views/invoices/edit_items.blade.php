<div class="col-lg-12">

    <div class="row">
        <div class="table-responsive invoice-items-scroll w-100">
            <table class="table table-bordered table-md" id="itemsTable">
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
                        <th style="text-align: center; width: 5%;" class="pr-0">Vat</th>
                        <th class="text-center pr-0" style="width: 10%;">Net</th>
                        <th class="pr-0" style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody id="itemRows">

                    @forelse ($salesItems as $index => $item)
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

                            <td class="p-1 m-0" style="width:25%;">
                                <select class="form-select serviceSelect" style="width:100%;"
                                    name="itemsArr[{{ $index }}][service_id]" required>
                                    <option value="" disabled selected>Select a service .</option>
                                    @if ($invoice->project)
                                        @foreach ($invoice->project['services'] as $projectService)
                                            @php
                                                $matchingService = $services->firstWhere(
                                                    'id',
                                                    $projectService['service_id'],
                                                );
                                            @endphp

                                            @if ($matchingService && $matchingService->item_group_id == $item['category_id'])
                                                <option value="{{ $matchingService->id }}"
                                                    data-item-number="{{ $matchingService['item_number'] }}"
                                                    @if ($matchingService->id == $item->service_id) selected @endif>
                                                    {{ $matchingService['title'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
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
                                <input type="number" class="form-control text-right tax" required value="15"
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
                    @empty
                        <tr class="sales-items-empty-placeholder">
                            <td colspan="12" class="text-center">No items found</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-info" id="addRow"><i class="fa fa-plus"></i> Add Row</button>
        <!--<button type="button" class="btn btn-success ml-2" id="addItem"><i class="fa fa-plus"></i> Add Item</button>-->
    </div>

    <div class="d-flex align-items-end pt-1 flex-nowrap" style="overflow-x:auto; gap:8px;">

        <!-- Subtotal -->
        <div style="width: 130px;">
            <label for="subtotal" class="form-label text-dark mb-1">Subtotal</label>
            <input class="form-control text-right" id="subtotal" value="{{ $invoice->subtotal ?? 0 }}" readonly>
        </div>

        <!-- Absent Deduction -->
        <div style="width: 150px;">
            <label for="absentDeduction" class="form-label text-dark mb-1">Absent Deduction</label>
            <input type="number" name="absent_deduction" id="absentDeduction"
                value="{{ $invoice->absent_deduction ?? 0 }}" class="form-control text-right deduction-field">
        </div>

        <!-- Allowance Deduction -->
        <div style="width: 160px;">
            <label for="allowanceDeduction" class="form-label text-dark mb-1">Allowance Deduction</label>
            <input type="number" name="allowance_deduction" id="allowanceDeduction"
                value="{{ $invoice->allowance_deduction ?? 0 }}" class="form-control text-right deduction-field">
        </div>

        <!-- Damage Deduction -->
        <div style="width: 150px;">
            <label for="damageDeduction" class="form-label text-dark mb-1">Damage Deduction</label>
            <input type="number" name="damage_deduction" id="damageDeduction"
                value="{{ $invoice->damage_deduction ?? 0 }}" class="form-control text-right deduction-field">
        </div>

        <!-- Total Discount -->
        <div style="width: 220px;">
            <label for="totalDiscount" class="form-label text-dark mb-1">Total Discount</label>
            <div class="d-flex">
                <select name="discount_type" id="discount_type" class="form-control text-center me-1"
                    style="width: 55px; font-weight:bold;">
                    <option value="1" {{ $invoice->discount_type == 1 ? 'selected' : '' }}>$</option>
                    <option value="0" {{ $invoice->discount_type == 0 ? 'selected' : '' }}>%</option>
                </select>

                <select name="percentage_discount" id="percentage_discount" class="form-control text-center me-1"
                    style="display: {{ $invoice->discount_type == 1 ? 'none' : 'block' }}; width:80px;">
                    @for ($i = 1; $i < 100; $i++)
                        <option value="{{ $i }}"
                            {{ $invoice->discount_type == 0 && $invoice->percentage_discount == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>

                <input type="number" name="final_discount" id="totalDiscount"
                    value="{{ $invoice->discount ?? 0 }}" class="form-control text-right">
            </div>
        </div>

        <!-- Total Taxable -->
        <div style="width: 130px;">
            <label for="taxable" class="form-label text-dark mb-1">Total Taxable</label>
            <input type="text" id="taxable" value="{{ $invoice->taxable ?? 0 }}"
                class="form-control text-right" readonly>
        </div>

        <!-- Total VAT -->
        <div style="width: 110px;">
            <label for="totalVAT" class="form-label text-dark mb-1">Total VAT</label>
            <input type="text" id="totalVAT" value="{{ $invoice->total_vat ?? 0 }}"
                class="form-control text-right" readonly>
        </div>

        <!-- Round Off (conditional) -->
        @if ($isRound)
            <div style="width: 120px;">
                <label for="adjustment" class="form-label text-dark mb-1">Round Off</label>
                <input type="number" name="adjustment" id="adjustment" value="{{ $invoice->adjustment ?? 0 }}"
                    class="form-control text-right" readonly>
            </div>
        @endif

        <!-- Net Amount -->
        <div style="width: 140px;">
            <label for="netTotal" class="form-label text-dark mb-1">Net Amount</label>
            <input type="text" id="netTotal" value="{{ $invoice->net_total ?? 0 }}"
                class="form-control text-right" readonly>
        </div>

        <input type="hidden" name="total_amount_new" id="totalAmt" value="{{ $invoice->total_amount_new ?? 0 }}">
    </div>

    <!-- Totals Section (Similar to the "add" form) -->
    <div class="row justify-content-between mt-3">
        <div class="col-md-12 p-0 m-0">
            <div class="col-md-6 p-0 m-0">
                {{-- <select class="form-control" id="terms_dropdown">
                    <option value=""></option>
                    @foreach ($terms as $key => $term)
                        <option value="{{ $key }}">{{ strip_tags($term) }}</option>
                    @endforeach
                </select> --}}
            </div>

            <div style="width: 100%;background:lightblue;color:black;height:40px;border-radius:5px;">
                <label for="terms_dropdown" style="font-weight: bold;line-height:40px;padding-left:10px;"> Terms &
                    Conditions</label>
            </div>
            <div class="mt-3" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered" id="terms_table">
                    <tbody>

                        <!-- Existing terms will be dynamically populated here on page load -->
                        @if ($invoice->project)
                            @foreach ($invoice->project?->terms as $index => $estimateTerm)
                                <tr data-id="{{ $estimateTerm['terms_id'] }}">
                                    <td style="width: 50px;">{{ $index + 1 }}</td>
                                    <td class="p-0 m-0 ">
                                        <textarea readonly name="description[]" class="form-control"
                                            style="height:120px;width:100%; background-color:white;">{{ $estimateTerm['description'] }}</textarea>
                                        <input type="hidden" name="terms[]"
                                            value="{{ $estimateTerm['terms_id'] }}">
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
