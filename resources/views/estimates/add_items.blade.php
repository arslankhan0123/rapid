{{-- <div class="col-lg-12 pt-2">

    <div class="row table-wrapper ">
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
                <!-- Default row -->
                <tr data-index="0">
                    <td class="p-1 text-center " style="width: 2%;">1</td>
                    <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number"
                            name="itemsArr[0]['item']" value=""></td>
                    <td class="p-1 m-0" style="width:25%;">
                        <select class="form-select categorySelect" style="width:100%;" name="itemsArr[0]['category_id']"
                            required>
                            <option value="" disabled selected>Select a Category</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}">
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-1 m-0" style="width:25%;">
                        <select class="form-select serviceSelect" style="width:100%;" name="itemsArr[0]['service_id']"
                            required>
                            <option value="" disabled selected>Select a service</option>
                            @foreach ($services as $id => $name)
                                <option value="{{ $name->id }}" data-item-number="{{ $name['item_number'] }}">
                                    {{ $name['title'] }}</option>
                            @endforeach
                        </select>
                    </td>


                    <td class="p-1 " style="width: 100px;"><input type="number"
                            class="form-control text-right quantity" required value="1"
                            name="itemsArr[0]['quantity']"></td>
                    <td class="p-1"><input type="number" class="form-control p-1 text-right rates" required
                            value="0.00" name="itemsArr[0]['rate']" style="width: 100px !important;"></td>
                    <td class="p-0">
                        <input type="number" class="form-control text-right discount p-0" required value="0.00"
                            name="itemsArr[0]['discount']" style="width:90px !important;background:white;border:none;"
                            readonly>
                    </td>
                    <td class="p-1 m-0" style="width:7%;"><input type="number"
                            class="form-control text-right taxable p-0" name="itemsArr[0]['taxable']" readonly
                            style="background:white;border:none;"></td>

                    <td class="p-0"><input type="number" class="form-control text-right tax p-0" value="15.00"
                            name="itemsArr[0]['tax']" readonly style="width:90px;background:white;border:none;"></td>
                    <td class="vat-amount text-end pr-0">0.00</td>
                    <td class="p-0 pr-2"><input type="number" readonly class="total-amount form-control text-right p-0"
                            style="background:white;border:none;width:90px;" name="itemsArr[0]['total']" value="0.00">
                    </td>
                    <td class="p-1 text-right">
                        <button type="button" class="btn text-danger "><i class="fa fa-times"></i></button>

                        <button type="button" class="btn text-info" data-toggle="modal" data-target="#headlineModal_0">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <!-- Modal for this row -->
                        <div class="modal fade" id="headlineModal_0" tabindex="-1" role="dialog"
                            style="text-align: left;">
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
                                                name="itemsArr[0]['headline']">
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" name="itemsArr[0]['subheadline']" style="height: 150px;"></textarea>
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

        <!-- Round Off -->

        @if ($isRound)
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

    <!-- Totals Table -->
    <div class="row justify-content-between mt-2">

        <div class="col-md-12 p-0 m-0 ">
            <div class="col-md-6 p-0 m-0">
                <label for="terms_dropdown">Select Terms & Conditions:</label>
                <select class="form-control" id="terms_dropdown">
                    <option value="0"></option>
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
                        <!-- Terms will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div> --}}


<div class="col-lg-12 pt-2">

    <div class="row table-wrapper ">
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
                <!-- Default row -->
                <tr data-index="0">
                    <td class="p-1 text-center " style="width: 2%;">1</td>
                    <td class="p-1" style="width: 15%;"><input type="text" class="form-control item-number"
                            name="itemsArr[0]['item']" value=""></td>
                    <td class="p-1 m-0" style="width:25%;">
                        <select class="form-select categorySelect" style="width:100%;" name="itemsArr[0]['category_id']"
                            required>
                            <option value="" disabled selected>Select a Category</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}">
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-1 m-0" style="width:25%;">
                        <select class="form-select serviceSelect" style="width:100%;" name="itemsArr[0]['service_id']"
                            required>
                            <option value="" disabled selected>Select a service</option>
                            @foreach ($services as $id => $name)
                                <option value="{{ $name->id }}" data-item-number="{{ $name['item_number'] }}">
                                    {{ $name['title'] }}</option>
                            @endforeach
                        </select>
                    </td>


                    <td class="p-1 " style="width: 100px;"><input type="number"
                            class="form-control text-right quantity" required value="1"
                            name="itemsArr[0]['quantity']"></td>
                    <td class="p-1"><input type="number" class="form-control p-1 text-right rates" required
                            value="0.00" name="itemsArr[0]['rate']" style="width: 100px !important;"></td>
                    <td class="p-0">
                        <input type="number" class="form-control text-right discount p-0" required value="0.00"
                            name="itemsArr[0]['discount']" style="width:90px !important;background:white;border:none;"
                            readonly>
                    </td>
                    <td class="p-0 pr-2"><input type="number" readonly class="total-amount form-control text-right p-0"
                            style="background:white;border:none;width:90px;" name="itemsArr[0]['total']" value="0.00">
                    </td>
                    <td class="p-1 text-right">
                        <button type="button" class="btn text-danger "><i class="fa fa-times"></i></button>

                        <button type="button" class="btn text-info" data-toggle="modal" data-target="#headlineModal_0">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <!-- Modal for this row -->
                        <div class="modal fade" id="headlineModal_0" tabindex="-1" role="dialog"
                            style="text-align: left;">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">More Description</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Headline</label>
                                            <input type="text" class="form-control" name="itemsArr[0]['headline']">
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" name="itemsArr[0]['subheadline']" style="height: 150px;"></textarea>
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

        <!-- Round Off -->

        @if ($isRound)
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

    <!-- Totals Table -->
    <div class="row justify-content-between mt-2">

        <div class="col-md-12 p-0 m-0 ">
            <div class="col-md-6 p-0 m-0">
                <label for="terms_dropdown">Select Terms & Conditions:</label>
                <select class="form-control" id="terms_dropdown">
                    <option value="0"></option>
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
                        <!-- Terms will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
