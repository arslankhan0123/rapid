<div class="col-lg-12">

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
                @foreach ($project->services as $index => $service)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="width: 20%;" class="p-1">
                            <input type="text" class="form-control" name="ref_no[]" required
                                value="{{ $service->ref_no }}">
                        </td>
                        <td class="p-1" style="width: 25%;">
                            <select class="form-control category-select select2" name="category_id[]" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ $service['category_id'] == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="p-1" style="width: 25%;">
                            <select class="form-control service-select select2" name="service_id[]" required>
                                <option value="">Select Service</option>
                                @foreach ($services as $indx => $sv)
                                    @if ($service->category_id == $sv->item_group_id)
                                        <option value="{{ $sv->id }}"
                                            {{ $service['service_id'] == $sv->id ? 'selected' : '' }}>
                                            {{ $sv->title }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1">
                            <input type="number" class="form-control text-right" name="unit_price[]" required step="any"
                                value="{{ $service->unit_price }}">
                        </td>
                        <td class="p-1">
                            <button type="button" class="btn text-danger remove-row">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    {{--  --}}
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-primary add-row" style="line-height: 31px;"><i
                class="fa fa-plus"></i> Add Row </button>
        <button type="button" class="btn btn-success ml-2" id="addItem"><i class="fa fa-plus"></i> Add Item</button>
    </div>
    {{-- <label for=""><strong>Select Employees</strong></label> --}}
    {{-- <table class="table table-bordered" id="employeeTable">
        <thead>
            <tr>
                <th>SL</th>
                <th>Iqama No<span class="required">*</span></th>
                <th>Employee Name</th>
                <th>Designation</th>
                <th>Hourly Rate<span class="required">*</span></th>
                <th>
                    <button type="button" class="btn btn-primary" id="addRowBtn">+</button>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($project->members as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <select name="members[]" class="form-control iqama-select" required>
                            <option value="">Select Iqama No</option>
                            @foreach ($data['members'] as $memberOption)
                                <option value="{{ $memberOption['id'] }}"
                                        data-name="{{ $memberOption['name'] }}"
                                        data-designation="{{ $memberOption['designation']->name ?? '' }}"
                                        {{ $memberOption['id'] == $member['user_id'] ? 'selected' : '' }}>
                                    {{ $memberOption['iqama_no'] }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" class="form-control employee-name" value="{{ $member['hourly_rate'] }}" readonly></td>
                    <td><input type="text" class="form-control designation" readonly></td>
                    <td><input type="number" name="hourly_rate[]" class="form-control hourly-rate" value="{{ $member['hourly_rate'] }}" required></td>
                    <td>
                        <button type="button" class="btn btn-danger deleteRowBtn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
</div>
