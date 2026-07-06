<div class="col-lg-12 pt-2">

    <div class="row">
        <table class="table table-responsive table-md page_contents" id="itemsTable">
            <thead>
                <tr style="padding: 0px;">
                    <th>S.</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Service </th>
                    <!--<th class="text-center">Headline </th>-->
                    <!--<th class="text-center">Description</th>-->
                    <th>Qty</th>
                    <th class="text-center pr-3">Rate </th>
                    <th style="text-align:center;" class="pr-0">Disc.</th>
                    <th class="text-center pr-0">Net</th>
                    <th class="pr-0">Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">

                <!-- Loop through salesItems and display existing data for editing -->
                @foreach ($estimate->salesItems as $index => $item)
                    <tr data-index="{{ $index }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="p-1" style="width: 15%;">
                            <input type="text" class="form-control item-number"
                                name="itemsArr[{{ $index }}][item]" value="{{ $item->item }}">
                        </td>
                        <td class="p-1 m-0" style="width:25%;">
                            <select class="form-select categorySelect" style="width:100%;"
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
                                <option value="" disabled selected>Select a service</option>
                                @foreach ($services as $id => $name)
                                    @if ($name['item_group_id'] == $item->category_id)
                                        <option value="{{ $name->id }}"
                                            data-item-number="{{ $name['item_number'] }}"
                                            @if ($name->id == $item->service_id) selected @endif>
                                            {{ $name['title'] }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>


                        <td class="p-1 " style="width: 100px;">
                            <input type="number" class="form-control text-right quantity" required
                                value="{{ $item->quantity }}" name="itemsArr[{{ $index }}]['quantity']">
                        </td>
                        {{-- {{dd( )}} --}}
                        <td class="p-1">
                            <input type="number" class="form-control text-right rates" required
                                value="{{ number_format($item->rate, 2, '.', '') }}"
                                name="itemsArr[{{ $index }}]['rate']" style="width: 100px !important;">
                        </td>
                        <td class=" pr-0  ">
                            <input type="number" class="form-control text-right discount"
                                value="{{ number_format($item->discount, 2, '.', '') }}"
                                name="itemsArr[{{ $index }}]['discount']"
                                style="width:90px !important;background:white;border:none;" readonly>
                        </td>

                        <td class="p-0 pr-2">
                            <input type="number" class="total-amount form-control text-right p-0"
                                name="itemsArr[{{ $index }}]['total']" value="{{ $item->total }}"
                                style="background:white;border:none;width:90px;" readonly>
                        </td>
                        <td class="p-1 text-right">
                            <button type="button" class="btn text-danger remove-row">
                                <i class="fa fa-times"></i>
                            </button>

                            <button type="button" class="btn text-info" data-toggle="modal"
                                data-target="#headlineModal_{{ $index }}">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>

                            <!-- Modal for this row -->
                            <div class="modal fade" id="headlineModal_{{ $index }}" tabindex="-1"
                                role="dialog" style="text-align: left;">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">More Description</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Headline</label>
                                                <input type="text" class="form-control"
                                                    name="itemsArr[{{ $index }}][headline]"
                                                    value="{{ $item->headline }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" name="itemsArr[{{ $index }}][subheadline]" style="height: 150px;">{{ $item->subheadline }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"
                                                data-dismiss="modal">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>
        <button type="button" class="btn btn-info" id="addRow"><i class="fa fa-plus"></i> Add Row</button>
        <button type="button" class="btn btn-success ml-2" id="addItem"><i class="fa fa-plus"></i> Add
            Service</button>
        <button type="button" class="btn btn-warning ml-2" id="addCategory"><i class="fa fa-plus"></i> Add
            Category</button>
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
                        <option value="1" {{ $estimate->discount_type == 1 ? 'selected' : '' }}>$
                        </option>
                        <option value="0" {{ $estimate->discount_type == 0 ? 'selected' : '' }}>%
                        </option>
                    </select>
                </div>
                <div class="col pl-1">
                    <div class="input-group" style="width: 100%;">
                        <select name="percentage_discount" id="percentage_discount"
                            style="display: {{ $estimate->discount_type == 1 ? 'none' : '' }};width:30%;"
                            class="form-control p-0 text-center">
                            <!-- Add percentage options from 5% to 100% in steps of 5 -->
                            @for ($i = 1; $i < 100; $i++)
                                <option value="{{ $i }}"
                                    {{ $estimate->discount_type == 0 ? ($estimate->percentage_discount == $i ? 'Selected' : '') : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        <input type="number" name="final_discount" id="totalDiscount"
                            {{ $estimate->discount_type == 0 ? 'readonly' : '' }}
                            value="{{ number_format($estimate->discount, 2) }}" class="form-control text-right"
                            style="width:70%;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Round Off -->
        @if ($isRound)
            <div class="col-md-2">
                <label for="adjustment" class="form-label text-dark ">Round Off</label>
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
                <table class="table table-bordered table-md" id="terms_table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">SL</th>
                            <th class="text-center">Description</th>
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

    </div>
</div>

{{-- <div class="col-lg-12 pt-2">

    <div class="row">
        <table class="table table-responsive table-md page_contents" id="itemsTable">
            <thead>
                <tr style="padding: 0px;">
                    <th>S.</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Service </th>
                    <!--<th class="text-center">Headline </th>-->
                    <!--<th class="text-center">Description</th>-->
                    <th>Qty</th>
                    <th class="text-center pr-3">Rate </th>
                    <th style="text-align:center;" class="pr-0">Disc.</th>
                    <th style="text-align: center;" class="pr-0 pr-2">Taxable</th>
                    <th style="text-align: center;width:5%;" class="pr-0">Vat %</th>
                    <th style="text-align: center;width:6%;" class="pr-0">Vat $</th>
                    <th class="text-center pr-0">Net</th>
                    <th class="pr-0">Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">

                @foreach ($estimate->salesItems as $index => $item)
                    <tr data-index="{{ $index }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="p-1" style="width: 15%;">
                            <input type="text" class="form-control item-number"
                                name="itemsArr[{{ $index }}][item]" value="{{ $item->item }}">
                        </td>
                        <td class="p-1 m-0" style="width:25%;">
                            <select class="form-select categorySelect" style="width:100%;"
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
                                <option value="" disabled selected>Select a service</option>
                                @foreach ($services as $id => $name)
                                    @if ($name['item_group_id'] == $item->category_id)
                                        <option value="{{ $name->id }}"
                                            data-item-number="{{ $name['item_number'] }}"
                                            @if ($name->id == $item->service_id) selected @endif>
                                            {{ $name['title'] }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>


                        <td class="p-1 " style="width: 100px;">
                            <input type="number" class="form-control text-right quantity" required
                                value="{{ $item->quantity }}" name="itemsArr[{{ $index }}]['quantity']">
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control text-right rates" required
                                value="{{ number_format($item->rate, 2, '.', '') }}"
                                name="itemsArr[{{ $index }}]['rate']" style="width: 100px !important;">
                        </td>
                        <td class=" pr-0  ">
                            <input type="number" class="form-control text-right discount"
                                value="{{ number_format($item->discount, 2, '.', '') }}"
                                name="itemsArr[{{ $index }}]['discount']"
                                style="width:90px !important;background:white;border:none;">
                        </td>

                        <td class="p-1 m-0" style="width: 7%;">
                            <input type="number" class="form-control text-right taxable p-0"
                                value="{{ number_format($item->taxable, 2) }}" readonly
                                style="background:white;border:none;">
                        </td>

                        <td class="p-0">
                            <input type="number" class="form-control text-right tax p-0" required value="15.00"
                                name="itemsArr[{{ $index }}]['tax']" readonly
                                style="width:90px;background:white;border:none;">
                        </td>
                        <td class="vat-amount text-end pr-0">{{ number_format($item->vat_amount, 2) }}</td>

                        <td class="p-0 pr-2">
                            <input type="number" class="total-amount form-control text-right p-0"
                                name="itemsArr[{{ $index }}]['total']" value="{{ $item->total }}"
                                style="background:white;border:none;width:90px;" readonly>
                        </td>
                        <td class="p-1 text-right">
                            <button type="button" class="btn text-danger remove-row">
                                <i class="fa fa-times"></i>
                            </button>

                            <button type="button" class="btn text-info" data-toggle="modal" data-target="#headlineModal_{{ $index }}">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>

                            <!-- Modal for this row -->
                            <div class="modal fade" id="headlineModal_{{ $index }}" tabindex="-1" role="dialog" style="text-align: left;">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">More Description</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Headline</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="itemsArr[{{ $index }}][headline]"
                                                    value="{{ $item->headline }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea
                                                    class="form-control"
                                                    name="itemsArr[{{ $index }}][subheadline]"
                                                    style="height: 150px;">{{ $item->subheadline }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>
        <button type="button" class="btn btn-info" id="addRow"><i class="fa fa-plus"></i> Add Row</button>
        <button type="button" class="btn btn-success ml-2" id="addItem"><i class="fa fa-plus"></i> Add Service</button>
        <button type="button" class="btn btn-warning ml-2" id="addCategory"><i class="fa fa-plus"></i> Add Category</button>
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
                        <option value="1" {{ $estimate->discount_type == 1 ? 'selected' : '' }}>$
                        </option>
                        <option value="0" {{ $estimate->discount_type == 0 ? 'selected' : '' }}>%
                        </option>
                    </select>
                </div>
                <div class="col pl-1">
                    <div class="input-group" style="width: 100%;">
                        <select name="percentage_discount" id="percentage_discount"
                            style="display: {{ $estimate->discount_type == 1 ? 'none' : '' }};width:30%;"
                            class="form-control p-0 text-center">
                            <!-- Add percentage options from 5% to 100% in steps of 5 -->
                            @for ($i = 1; $i < 100; $i++)
                                <option value="{{ $i }}"
                                    {{ $estimate->discount_type == 0 ? ($estimate->percentage_discount == $i ? 'Selected' : '') : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        <input type="number" name="final_discount" id="totalDiscount"
                            {{ $estimate->discount_type == 0 ? 'readonly' : '' }}
                            value="{{ number_format($estimate->discount, 2) }}" class="form-control text-right"
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

        <!-- Round Off -->
        @if ($isRound)
            <div class="col-md-2">
                <label for="adjustment" class="form-label text-dark ">Round Off</label>
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
                <table class="table table-bordered table-md" id="terms_table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">SL</th>
                            <th class="text-center">Description</th>
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

    </div>
</div> --}}
