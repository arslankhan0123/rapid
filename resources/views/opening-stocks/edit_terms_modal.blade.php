<div id="termsModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg " style="margin-top:12%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="width: 100%;padding-bottom:8px;">
                <h5 class="modal-title">Terms & Condtions</h5>
                <button type="button" aria-label="Close" class="close p-2" style="font-size: 30px;"
                    data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">

                <div class="container-fluid">

                    <div class="row">

                        <table class="table table-bordered " id="terms_table">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">SL</th>
                                    <th class="p-0" style="position: relative;">
                                        <!-- Default visible Select2 dropdown -->
                                        <select class="form-control " id="terms_dropdown" style="width: 100%;">
                                            <option value="">Select Terms & Conditions</option>
                                            @foreach ($terms as $key => $term)
                                                <option value="{{ $key }}"
                                                    data-full-text='{{ strip_tags($term) }}'>
                                                    {{ strlen(strip_tags($term)) > 50 ? substr(strip_tags($term), 0, 50) . '...' : strip_tags($term) }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <!-- Hidden Description span by default -->
                                        <span id="description_text" class="d-none ">Description</span>
                                    </th>
                                    <th style="width: 20px;">
                                        <!-- Toggle icon -->
                                        <span id="toggle_icon" style="cursor: pointer;">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                    </th>
                                </tr>


                            </thead>
                            <tbody>
                                <!-- Existing terms will be dynamically populated here on page load -->
                                @foreach ($estimate->terms as $index => $estimateTerm)
                                    <tr data-id="{{ $estimateTerm['terms_id'] }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="p-0 m-0">
                                            <textarea name="terms_description[]" class="form-control" style="height:120px;width:100%;">{{ $estimateTerm['description'] }}</textarea>
                                            <input type="hidden" name="terms[]"
                                                value="{{ $estimateTerm['terms_id'] }}">
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
    </div>
</div>
