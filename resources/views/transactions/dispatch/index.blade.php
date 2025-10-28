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
                <div class="d-flex justify-content-between card-header mt-2">
                    <h5 class="card-title">
                        Dispatch Entry
                    </h5>

                    <!-- Button trigger modal -->
                    <button type="button" class="btn" data-toggle="modal" data-target="#uploadModal">
                        <i data-feather="upload" class="text-primary" style="font-size: 24px;"></i><b> Upload </b>
                    </button>

                </div>
                <div class="card-body">
                    <form action="{{ route('dispatch-plan.store') }}" method="POST" id="dispatchForm">
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
                                            name="type"
                                            id="dispatch_type"
                                            class="js-example-basic-single form-select"
                                            onchange="dispatchType()"
                                            required
                                        >
                                            <option value="">----Select----</option>
                                            <option value="sales">Sales</option>
                                            <option value="transfer">Transfer</option>
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
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="date" class="col-sm-4 control-label">
                                        Date
                                        <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="date" name="date" id="date" class="form-control" required>
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
                                        <select name="location_id" id="location_id" class="js-example-basic-single form-select mandatory" style="width:100%" required>
                                            <option value="">--select--</option>
                                            @forelse ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
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
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
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
                                        <select name="item_id" id="item_id" class="js-example-basic-single form-control mandatory" style="width:100%" onchange="getItemSpq()">
                                            <option value="">--select--</option>
                                            @forelse ($fgItems as $fgItem)
                                                <option value="{{ $fgItem->id }}">{{ $fgItem->item_code }}/{{ $fgItem->name }}</option>
                                            @empty
                                                <option value="" disabled>No Items Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <b><span id="item-spq" style="display: none" class="text-danger"></span></b>
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
                                </tbody>
                            </table>
                        </div>

                        <input type="hidden" id="item_new_name">

                        <div class="card-body">
                            <input type="hidden" name="items_json" id="items_json">
                            <button type="submit" class="btn btn-primary">Save</button>
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
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excelFile" class="form-label"></label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <span class="mt-2">You can download excel in predefined format by <a href="{{ URL::to( '/excel_templates/transaction_templates/grn_template.xlsx')}}" class="text-primary ">Clicking Here</a></span>
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

    function getItemSpq(){
        var itemId = $('#item_id').val();

        $.ajax({
            type: "POST",
            url: "{{route('ajax.getspqquantity')}}",
            data: {
                item_id : itemId
            },
            dataType: "json",
            success: function (response) {
                console.log("response: " + response);

                if (response.spq_quantity) {
                    $('#item-spq').text("Item SPQ: " + response.spq_quantity).show();
                } else {
                    console.log("SPQ Quantity not found for this item.");
                }
            },
            error: function (xhr, status, error) {
                console.log("An error occurred: " + error);
            }
        });
    }

    let itemList = []; // array to hold added items
    let rowCount = 0;

    // Add to Grid
    $('#addtogrid').on('click', function () {

        $('#item-spq').hide();

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

        // Prevent duplicate items (optional)
        const exists = itemList.some(i => i.item_id === itemId);
        if (exists) {
            sweetAlertMessage('warning', 'Already Exists', 'Item already added to grid!');
            return;
        }

        // Add item to array
        rowCount++;
        const itemData = {
            item_id: itemId,
            uom_id: uomId,
            quantity: quantity
        };
        itemList.push(itemData);

        // Append to table
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

        // Update hidden count
        $('#count').val(itemList.length);

        // Clear fields after adding
        $('#item_id').val('').trigger('change');
        $('#uom_id').val('').trigger('change');
        $('#quantity').val('');
    });

    // Remove a row
    function removeRow(rowNo) {
        itemList = itemList.filter(item => item.sl_no !== rowNo);
        $('#row_' + rowNo).remove();

        // Reorder serial numbers
        $('#dispatchGridBody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
        $('#count').val(itemList.length);
    }

    // Reset all fields and grid
    function rowreset() {
        itemList = [];
        rowCount = 0;
        $('#dispatchGridBody').empty();
        $('#item_id').val('').trigger('change');
        $('#uom_id').val('').trigger('change');
        $('#quantity').val('');
        $('#count').val('');
    }

    $('#dispatchForm').on('submit', function (e) {
        if (itemList.length === 0) {
            e.preventDefault();
            sweetAlertMessage('warning', 'Please add at least one item before saving.');
            return false;
        }

        $('#items_json').val(JSON.stringify(itemList));
    });
</script>

@endpush
