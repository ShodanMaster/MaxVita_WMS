@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/@mdi/css/materialdesignicons.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
@include('sweetalert::alert')

<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Dispatch Edit
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dispatch.update', $dispatch->id) }}" method="POST" id="dispatchForm">
                        @method('PATCH')
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="dispatch_type" class="col-sm-4 control-label">
                                        Type
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select
                                            name="dispatch_type"
                                            id="dispatch_type"
                                            class="js-example-basic-single form-select"
                                            onchange="dispatchType()"
                                            required
                                        >
                                            <option value="">----Select----</option>
                                            <option value="sales" @if($dispatch->dispatch_type == "sales") selected @endif >Sales</option>
                                            <option value="transfer" @if($dispatch->dispatch_type == "transfer") selected @endif>Transfer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="dispatch_number" class="col-sm-4 control-label">
                                        Dispatch Number
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <input
                                            type="text"
                                            name="dispatch_number"
                                            id="dispatch_number"
                                            class="form-control form-control-sm"
                                            value="{{ $dispatch->dispatch_number }}"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="dispatch_date" class="col-sm-4 control-label">
                                        Date
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" value="{{ $dispatch->dispatch_date }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="location_id" class="col-sm-4 control-label">
                                        Location
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="location_id" id="location_id" class="js-example-basic-single form-select mandatory" style="width:100%" required onchange="filterItem()">
                                            <option value="">--select--</option>
                                            @forelse ($locations as $location)
                                                <option value="{{ $location->id }}" @if ($dispatch->from_location_id == $location->id) selected @endif>{{ $location->name }}</option>
                                            @empty
                                                <option value="" disabled>No Locations Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6" id="customer" style="display: block">
                                <div class="form-group row">
                                    <label for="customer_id" class="col-sm-4 control-label">
                                        Customer
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="customer_id" id="customer_id" class="js-example-basic-single form-select mandatory" style="width:100%" required>
                                            <option value="">--select--</option>
                                            @forelse ($customers as $customer)
                                                <option value="{{ $customer->id }}" @if ($dispatch->dispatch_type == "sales" && $dispatch->dispatch_to_id == $customer->id) selected @endif>{{ $customer->name }}</option>
                                            @empty
                                                <option value="" disabled>No Customers Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6" id="location" style="display: none">
                                <div class="form-group row">
                                    <label for="to_location_id" class="col-sm-4 control-label">
                                        To Location
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="to_location_id" id="to_location_id" class="js-example-basic-single form-select mandatory" style="width:100%" required>
                                            <option value="">--select--</option>
                                            @forelse ($locations as $location)
                                                <option value="{{ $location->id }}" @if ($dispatch->dispatch_type == "transfer" && $dispatch->dispatch_to_id == $location->id) selected @endif>{{ $location->name }}</option>
                                            @empty
                                                <option value="" disabled>No Locations Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-header">
                            <h3 class="card-title">Product Details</h3>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="item_id" class="col-sm-4 control-label">
                                        Part No / Item Name
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="item_id" id="item_id" class="js-example-basic-single form-control mandatory" style="width:100%" onchange="getItemUOM()">
                                            <option value="">--select--</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="item-details" style="display: none">
                                    <b><span id="item-uom" class="text-danger"></span><span id="item-stock" class="text-danger"></span></b>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="uom" class="col-sm-4 control-label">UOM</label>
                                    <div class="col-sm-8">
                                        <select name="uom_id" id="uom_id" class="js-example-basic-single form-control mandatory" style="width:100%">
                                            <option value="">--select--</option>
                                            @forelse ($uoms as $uom)
                                                <option value="{{ $uom->id }}">{{ $uom->name }}</option>
                                            @empty
                                                <option value="" disabled>No Uoms Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="quantity" class="col-sm-4 control-label">Quantity</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="quantity" id="quantity" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <input type="button" value="ADD TO GRID" class="btn btn-primary" id="addtogrid">
                            <input type="hidden" id="count" name="count">
                            &nbsp;
                            <input type="button" name="reset" class="btn btn-default" value="Reset" onclick="rowreset()">
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="dispatchGrid">
                                <thead>
                                    <tr>
                                        <th>Sl.no</th>
                                        <th>Part No/Item Name</th>
                                        <th>Quantity</th>
                                        <th>UOM</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dispatchGridBody">
                                    @foreach ($dispatch->dispatchSubs as $dispatchSub)
                                        <tr id="row_{{ $loop->iteration }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $dispatchSub->item->item_code }}/{{ $dispatchSub->item->name }}</td>
                                            <td>{{ $dispatchSub->total_quantity }}</td>
                                            <td>{{ $dispatchSub->uom->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow({{ $loop->iteration }})">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                {{-- Hidden input fields go here --}}
                                <div id="hiddenItemInputs">
                                    @foreach ($dispatch->dispatchSubs as $dispatchSub)
                                        <input type="hidden" name="items[{{ $loop->iteration }}][item_id]" value="{{ $dispatchSub->item_id }}" data-row="{{ $loop->iteration }}">
                                        <input type="hidden" name="items[{{ $loop->iteration }}][uom_id]" value="{{ $dispatchSub->uom_id }}" data-row="{{ $loop->iteration }}">
                                        <input type="hidden" name="items[{{ $loop->iteration }}][quantity]" value="{{ $dispatchSub->total_quantity }}" data-row="{{ $loop->iteration }}">
                                    @endforeach
                                </div>
                            </table>
                        </div>

                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="reset" class="btn btn-default">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Dispatch Excel Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dispatch-excel-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excelFile" class="form-label"></label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <span class="mt-2">You can download excel in predefined format by <a href="{{ URL::to( '/excel_templates/transaction_templates/dispatch_template.xlsx')}}" class="text-primary ">Clicking Here</a></span>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-default">Clear</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ asset('assets/plugins/inputmask/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/typeahead-js/typeahead.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
<script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-maxlength.js') }}"></script>
<script src="{{ asset('assets/js/inputmask.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/typeahead.js') }}"></script>
<script src="{{ asset('assets/js/tags-input.js') }}"></script>
<script src="{{ asset('assets/js/dropzone.js') }}"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script src="{{ asset('assets/js/data-table.js') }}"></script>

<script>
    $(document).ready(function () {

        // Trigger dispatch type (Sales / Transfer)
        dispatchType();

        // Trigger item filtering when location is already selected
        const locationId = $('#location_id').val();
        if (locationId) {
            filterItem();
        }

        // If an item is already selected (edit case), trigger UOM and stock load
        const itemId = $('#item_id').val();
        if (itemId) {
            getItemUOM();
        }
    });

    function dispatchType(){
        const type = $('#dispatch_type').val();

        if (type === 'sales') {
            $('#customer').show();
            $('#location').hide();

            $('#customer').find('select, input').attr('required', true);
            $('#location').find('select, input').removeAttr('required');

        } else if (type === 'transfer') {

            $('#customer').hide();
            $('#location').show();

            $('#location').find('select, input').attr('required', true);
            $('#customer').find('select, input').removeAttr('required');

        } else {
            $('#customer, #location').hide();
            $('#customer, #location').find('select, input').removeAttr('required');
        }
    }

    function filterItem(){

        var  locationId = $('#location_id').val();

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getgrnitems') }}",
            data: {
                grn_type : 'FG',
                location_id : locationId,
            },
            dataType: "json",
            success: function (response) {
                $('#item_id').empty();
                $('#item_id').append('<option value="">--select--</option>');
                if (response && response.length > 0) {
                    response.forEach(function(item){

                        $('#item_id').append(
                            `<option value="${item.id}">${item.item_code}/${item.name}</option>`
                        );
                    });

                }
            }
        });
    }

    async function getInStock(itemId) {
        return $.ajax({
            type: "POST",
            url: "{{ route('ajax.item-in-stock') }}",
            data: {
                type : 'dispatch',
                item_id: itemId,
            },
            dataType: "json"
        });
    }

    async function getItemUOM() {
        const itemId = $('#item_id').val();

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getitemuom') }}",
            data: { item_id: itemId },
            dataType: "json",
            success: async function (response) {
                if (response) {
                    $('#item-details').show();
                    $('#item-uom').text("Item UOM: " + response.uom_name + " | ");

                    const inStock = await getInStock(itemId);

                    if (inStock.in_stock) {
                        $('#item-stock')
                            .removeClass("text-danger")
                            .addClass("text-success")
                            .text("In Stock: " + inStock.count);
                    } else {
                        $('#item-stock')
                            .removeClass("text-success")
                            .addClass("text-danger")
                            .text("Out Of Stock");
                    }
                }
            }
        });
    }

    let rowCount = $('#dispatchGridBody tr').length;

    $('#addtogrid').on('click', function () {
        $('#item-details').hide();

        const itemId = $('#item_id').val();
        const itemText = $('#item_id option:selected').text();
        const uomId = $('#uom_id').val();
        const uomText = $('#uom_id option:selected').text();
        const quantity = $('#quantity').val();

        // Validation
        if (!itemId || !uomId || !quantity) {
            sweetAlertMessage('warning', 'Fill Fields', 'Please fill all mandatory fields!');
            return;
        }

        // Prevent duplicate items
        const exists = $(`#hiddenItemInputs input[name^="items["][name$="[item_id]"][value="${itemId}"]`).length > 0;
        if (exists) {
            sweetAlertMessage('warning', 'Already Exists', 'Item already added to grid!');
            return;
        }

        // Increase row count
        rowCount++;

        // Append to table body
        const row = `
            <tr id="row_${rowCount}">
                <td>${rowCount}</td>
                <td>${itemText}</td>
                <td>${quantity}</td>
                <td>${uomText}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">Delete</button>
                </td>
            </tr>
        `;
        $('#dispatchGridBody').append(row);

        // Append hidden inputs separately
        const hiddenInputs = `
            <input type="hidden" name="items[${rowCount}][item_id]" value="${itemId}" data-row="${rowCount}">
            <input type="hidden" name="items[${rowCount}][uom_id]" value="${uomId}" data-row="${rowCount}">
            <input type="hidden" name="items[${rowCount}][quantity]" value="${quantity}" data-row="${rowCount}">
        `;

        $('#hiddenItemInputs').append(hiddenInputs);

        // Update count
        $('#count').val($('#dispatchGridBody tr').length);

        // Clear fields
        $('#item_id').val('').trigger('change');
        $('#uom_id').val('').trigger('change');
        $('#quantity').val('');
    });

    function removeRow(rowId) {
        $(`#row_${rowId}`).remove();
        $(`#hiddenItemInputs input[data-row="${rowId}"]`).remove();

        // Reorder visible rows (optional, visual only)
        $('#dispatchGridBody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });

        $('#count').val($('#dispatchGridBody tr').length);
    }

    // Reset all fields and grid
    function rowreset() {
        rowCount = 0;
        $('#dispatchGridBody').empty();
        $('#hiddenItemInputs').empty();
        $('#item_id').val('').trigger('change');
        $('#uom_id').val('').trigger('change');
        $('#quantity').val('');
        $('#count').val('');
    }

    // Form submission validation
    $('#dispatchForm').on('submit', function (e) {
        const itemCount = $('#hiddenItemInputs input[name^="items["][name$="[item_id]"]').length;

        if (itemCount === 0) {
            e.preventDefault();
            sweetAlertMessage('warning', 'Empty Items', 'Please add at least one item before saving.');
            return false;
        }
    });

</script>

@endpush
