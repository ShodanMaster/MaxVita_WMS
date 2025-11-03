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

<style>
    #bin_code {
        text-transform: uppercase;
    }
</style>

@section('content')
@include('sweetalert::alert')
<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title">
                        Production Plan Entry
                    </h5>
                    <button type="button" class="btn" data-toggle="modal" data-target="#uploadModal">
                        <i data-feather="upload" class="text-primary" style="font-size: 24px;"></i><b> Upload </b>
                    </button>
                </div>
                <div class="card-body">
                    <form id="productionPlan" action="{{ route('production-plan.store')}}" method="POST">
                        @csrf
                        <!-- PP Number & Production Date -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="control-label">
                                        PP Number
                                    </label>
                                    <input
                                        type="text"
                                        disabled
                                        class="form-control form-control-sm"
                                        value="{{ $productionNumber }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="plan_date" class="control-label">
                                        Production Date <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="date"
                                        name="plan_date"
                                        id="plan_date"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('plan_date') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- FG Item Code & Quantity -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="fgItem" class="form-label">
                                        FG Item Code/Item Description <font color="#FF0000">*</font>
                                    </label>
                                    <select id="fg-item" name="fgItem" class="form-control select2 form-control-sm">
                                        <option value="" selected disabled>-- select --</option>
                                        @forelse ($fgItems as $fgItem)
                                            <option value="{{$fgItem->id}}">
                                                {{$fgItem->item_code}}/{{ $fgItem->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No Items Available</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="quantity" class="control-label">
                                        Quantity <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="number"
                                        name="quantity"
                                        id="quantity"
                                        class="form-control form-control-sm"
                                        value="{{ old('quantity') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- RM Product Details Section -->
                        <div id="griddiv">
                            <div class="card-header">
                                <h3 class="card-title">RM Product Details</h3>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="rmItem" class="control-label">
                                            RM Item Code/Item Description <font color="#FF0000">*</font>
                                        </label>
                                        <select id="rmItem" name="rmItem" class="form-control select2 form-control-sm">
                                            <option value="" selected disabled>-- select --</option>
                                            @forelse ($rmItems as $rmItem)
                                                <option value="{{$rmItem->id}}">
                                                    {{$rmItem->item_code}}/{{ $rmItem->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No Items Available</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div id="item-details" style="display: none">
                                        <b><span id="item-uom" class="text-danger"></span><span id="item-stock" class="text-danger"></span></b>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="total_quantity" class="control-label">
                                            RM Quantity <font color="#FF0000">*</font>
                                        </label>
                                        <input
                                            type="number"
                                            step="1"
                                            id="total_quantity"
                                            class="form-control form-control-sm mandatory"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Add to Grid Section -->
                            <div class="card-body">
                                <button type="button" value="ADD TO GRID" class="btn btn-primary" id="addtogrid" onclick="addToGrid()">ADD TO GRID</button>
                                <input type="hidden" id="count" name="count" />
                                <button type="button" name="reset" class="btn btn-default" onclick="rowReset()">Reset</button>
                            </div>
                        </div>

                        <!-- Table for GRN Details -->
                        <div class="table-responsive">
                            <table class="table" id="grngrid">
                                <thead>
                                    <tr>
                                        <th>Sl.no</th>
                                        <th>Part No/Item Name</th>
                                        <th>Rm Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="grngridbody"></tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="button" class="btn btn-primary" id="submitButton" onclick="checkData()">Save</button>
                            <button type="reset" class="btn btn-default">Clear</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Modal for Excel Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Production Plan Excel Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('production-plan-excel-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excelFile" class="form-label"></label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <span class="mt-2">You can download excel in predefined format by <a href="{{ URL::to( '/excel_templates/transaction_templates/production_plan_template.xlsx')}}" class="text-primary ">Clicking Here</a></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="uploadButton">Upload</button>
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
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        async function getInStock(itemId) {
            return $.ajax({
                type: "POST",
                url: "{{ route('ajax.item-in-stock') }}",
                data: { item_id: itemId },
                dataType: "json"
            });
        }

        async function getItemUOM() {
            const itemId = $('#rmItem').val();

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

        let itemCount = 0;

        // Define the addToGrid function
        function addToGrid() {
            $('#item-uom').hide();

            let itemId = $('#rmItem').val();
            let itemName = $('#rmItem option:selected').text();
            let totalQuantity = $('#total_quantity').val();

            if (!itemId || !totalQuantity) {
                sweetAlertMessage('warning', 'Fill Fields', 'Please fill out all fields before adding to the grid!');
                return;
            }

            // Check if the item already exists in the grid
            if ($('#grngridbody').find('tr').filter(function() {
                return $(this).find('td').eq(1).text() === itemName;
            }).length > 0) {
                sweetAlertMessage('warning', 'Already Exists', 'This item has already been added!');
                return;
            }

            itemCount++;

            // Add item to grid table
            $('#grngridbody').append(`
                <tr data-item-id="${itemId}">
                    <td>${itemCount}</td>
                    <td>${itemName}</td>
                    <td>${totalQuantity}</td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                </tr>
            `);

            // Add hidden inputs for form submission
            $('<input>').attr({
                type: 'hidden',
                name: `rmItems[${itemCount}][item_id]`,
                value: itemId
            }).appendTo('form');

            $('<input>').attr({
                type: 'hidden',
                name: `rmItems[${itemCount}][total_quantity]`,
                value: totalQuantity
            }).appendTo('form');

            // Reset fields after adding to grid
            $('#rmItem').val('').trigger('change');
            $('#total_quantity').val('');
        }

        // Define the checkData function for form validation
        function checkData() {
            var productionDate = document.getElementById('plan_date').value;
            var fgItem = document.getElementById('fg-item').value;
            var quantity = document.getElementById('quantity').value;
            var itemCount = document.getElementById('grngridbody').rows.length;

            if (!productionDate) {
                sweetAlertMessage('warning', 'Select Production Date', 'Please Select Production Date!');
                return false;
            }
            else if (!fgItem) {
                sweetAlertMessage('warning', 'Select FG Item', 'Please Select FG Item!');
                return false;
            }
            else if (!quantity) {
                sweetAlertMessage('warning', 'Enter Quantity', 'Please Enter Quantity!');
                return false;
            }
            else if (itemCount == 0) {
                sweetAlertMessage('warning', 'Empty Grid', 'Please add items to the grid!');
                return false;
            }
            else {
                document.getElementById('productionPlan').submit();
            }
        }

        $('#rmItem').change(function() {
            getItemUOM();
        });

        // Bind the checkData function to the Save button
        $('#submitButton').click(function() {
            checkData();
        });

        // Bind the addToGrid function to the Add to Grid button
        $('#addtogrid').click(function() {
            addToGrid();
        });

        $(document).on('click', '.remove-item', function() {

            let itemId = $(this).closest('tr').data('item-id');
            $(this).closest('tr').remove();

            $(`input[name="items[][item_id]"][value="${itemId}"]`).remove();
            $(`input[name="items[][total_quantity]"][value="${itemId}"]`).remove();

            itemCount--;
        });
    });

    $('#uploadModal form').on('submit', function() {
        $('#uploadButton').prop('disabled', true).text('Uploading...');
    });
</script>
@endpush
