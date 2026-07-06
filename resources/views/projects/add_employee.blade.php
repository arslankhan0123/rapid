<div class="col-lg-12">
    <label for=""><strong>Items </strong></label>
    <div class="row">
        <table class="table" id="itemsTable">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Item Ref No <span class="required">*</span></th>
                    <th>Category <span class="required">*</span></th>
                    <th>Services <span class="required">*</span></th>
                    <th>Unit Price <span class="required">*</span></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="itemRows">
                <tr>
                    <td>1</td>
                    <td style="width: 20%;" class="p-1">
                        <input type="text" class="form-control" name="ref_no[]" required>
                    </td>
                    <td class="p-1" style="width: 25%;">
                        <select class="form-control category-select select2" name="category_id[]" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="p-1" style="width: 25%;">
                        <select class="form-control service-select select2" name="service_id[]" required>
                            <option value="">Select Service</option>
                            @foreach ($services as $id => $name)
                                <option value="{{ $name['id'] }}" data-category-id="{{ $name['item_group_id'] }}">
                                    {{ $name['title'] }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-1">
                        <input type="number" class="form-control text-right" name="unit_price[]" required
                            step="any">
                    </td>
                    <td class="p-1">
                        <button type="button" class="btn text-danger remove-row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-primary add-row" style="line-height: 30px;"><i
                class="fa fa-plus"></i> Add Row</button>
        <button type="button" class="btn btn-success ml-2" id="addItem"><i class="fa fa-plus"></i> Add Item</button>


    </div>
</div>
