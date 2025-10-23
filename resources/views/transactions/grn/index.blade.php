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
@include('messages')

<div class="container">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="d-flex justify-content-between card-header mt-2">
                    <h5 class="card-title">
                        GRN Entry
                    </h5>

                    <!-- Button trigger modal -->
                    <button type="button" class="btn" data-toggle="modal" data-target="#uploadModal">
                        <i data-feather="upload" class="text-primary" style="font-size: 24px;"></i><b> Upload </b>
                    </button>

                </div>
                <div class="card-body">
                    <form id="grnForm" action="{{ route('grn.store')}}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="grn_number" class="col-sm-4 control-label">
                                        GRN Number
                                    </label>
                                    <div class="col-sm-8">
                                        <input
                                            type="text"
                                            disabled
                                            class="form-control form-control-sm"
                                            value="{{$grnNumber}}"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="grn_type" class="col-sm-4 control-label">
                                        GRN Type <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="grn_type" id="grn_type" class="js-example-basic-single form-control mandatory" onchange="filterItem()">
                                            <option value="" disabled selected>--select grn type --</option>
                                            <option value="RM" {{ old('grn_type') == 'RM' ? 'selected' : '' }}>RM</option>
                                            <option value="FG" {{ old('grn_type') == 'FG' ? 'selected' : '' }}>FG</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="vendor_id" class="col-sm-4 control-label">
                                        Vendor Name <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="vendor_id" id="vendor_id" class="js-example-basic-single form-control mandatory" required onchange="getPurchaseNumber();">
                                            <option value="" disabled selected>--select--</option>
                                            @forelse ($vendors as $vendor)
                                                <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                            @empty
                                                <option value="" disabled>No Vendors Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="location_id" class="col-sm-4 control-label">
                                        Location <font color="#FF0000">*</font>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="location_id" id="location_id" class="js-example-basic-single form-select mandatory" onchange="filterItem();" required>
                                            <option value="" disabled selected>--select--</option>
                                            @forelse ($locations as $location)
                                                <option value="{{$location->id}}">{{$location->name}}</option>
                                            @empty
                                                <option value="" disabled>No Locations Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="invoice_number" class="col-sm-4 control-label">
                                        Invoice Number
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="invoice_number" id="invoice_number" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="invoice_date" class="col-sm-4 control-label">
                                        Invoice Date
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="date" name="invoice_date" id="invoice_date" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="remark" class="col-sm-4 control-label">
                                        Remark
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="prn" class="col-sm-4 control-label">
                                        PRN Print
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="checkbox" name="prn" id="prn" class="custom-class">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="griddiv">
                            <div class="card-header">
                                <h3 class="card-title">Product Details</h3>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="purchase_number" class="col-sm-4 control-label">PO Number</label>
                                        <div class="input-group col-sm-8">
                                            <select class="js-example-basic-single form-select mandatory" style="width:100%" id="purchase_number" name="purchase_number" onchange="filterItem();">
                                                <option value="">--select--</option>
                                            </select>
                                            <input type="hidden" id="is_purchase" name="is_purchase"/>
                                            <input type="hidden" id="purchase_item_quantity" name="purchase_item_quantity"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="category_id" class="col-sm-4 control-label">Category</label>
                                        <div class="col-sm-8">
                                            <select class="js-example-basic-single form-select mandatory" style="width:100%" id="category_id" onchange="filterItem();">
                                                <option value="" selected>--select--</option>
                                                @forelse ($categories as $category)
                                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                                @empty
                                                    <option value="" disabled>No Categories Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="item_id" class="col-sm-4 control-label">Part No / Item Name <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <select class="js-example-basic-single form-control mandatory" style="width:100%" id="item_id" onchange="getItemUOM();">
                                                <option value="">--select--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="uom" class="col-sm-4 control-label">UOM</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="uom" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="batch_no" class="col-sm-4 control-label">Batch Number <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="batch_no" name="batch_number" value="{{ $batchNumber }}" class="form-control form-control-sm mandatory" readonly />
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="price" class="col-sm-4 control-label">Price <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="price" class="form-control form-control-sm mandatory"" />
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="dom" class="col-sm-4 control-label">DOM <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="date" id="dom" class="form-control form-control-sm mandatory" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="best_before_value" class="col-sm-4 control-label">Best Before <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="date" id="best_before_value" class="form-control form-control-sm mandatory date-field" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="spq" class="col-sm-4 control-label">SPQ <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="spq" class="form-control form-control-sm mandatory" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="total_quantity" class="col-sm-4 control-label">Total Quantity <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="1" id="total_quantity" class="form-control form-control-sm mandatory" onchange="totalBarcode()" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="number_of_barcodes" class="col-sm-4 control-label">Number Of Barcodes <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="1" id="number_of_barcodes" class="form-control form-control-sm mandatory" onchange="totalQuantity()" pattern="^[1-9][0-9]*$" value="1" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="row">
                            </div> --}}

                            <div class="card-body">
                                <input type="button" value="ADD TO GRID" class="btn btn-primary" id="addtogrid" onclick="addToGrid()" />
                                <input type="hidden" id="count" name="count" />
                                &nbsp;
                                <input type="button" name="reset" class="btn btn-default" value="Reset" onclick="rowReset()" />
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="grngrid">
                                <thead>
                                    <tr>
                                        <th>Sl.no</th>
                                        <th>Part No/Item Name</th>
                                        {{-- <th>price</th> --}}
                                        <th>Batch No</th>
                                        <th>DOM</th>
                                        <th>BBF</th>
                                        <th>SPQ</th>
                                        <th style="display: none" id="balanceth">Balance <br> Quantity</th>
                                        <th>GRN <br> Quantity</th>
                                        <th>No of Barcodes</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="grngridbody">
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="button" class="btn btn-primary" id="submitButton" onclick="checkData()">
                                Save
                            </button>
                            <input type="reset" class="btn btn-default" value="Clear" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">GRN Excel Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('grn-excel-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excelFile" class="form-label"></label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <span class="mt-2">You can download excel in predefined format by <a href="{{ URL::to( '/excel_templates/transaction_templates/grn_template.xlsx')}}" class="text-primary ">Clicking Here</a></span>
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



@endpush

<script>
    console.log("qwertyu: ", @json(session()->all()));
    $('.select2').select2();
    let itemCount = 0;

    function filterItem(){

        var  grnType = $('#grn_type').val();
        var  locationId = $('#location_id').val();
        var  categoryId = $('#category_id').val();
        var  purchaseNumber = $('#purchase_number').val();

        if(grnType == 'FG'){
            $('#number_of_barcodes').prop('readonly', true);
        }else{
            $('#number_of_barcodes').prop('readonly', false);
        }

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getgrnitems') }}",
            data: {
                grn_type : grnType,
                location_id : locationId,
                category_id : categoryId,
                purchase_number : purchaseNumber,
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

    function getPurchaseNumber(){
        var  vendorId = $('#vendor_id').val();

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getpurchasenumber') }}",
            data: {
                vendor_id : vendorId
            },
            dataType: "json",
            success: function (response) {
                if (response && response.length > 0) {
                    $('#purchase_number').empty()
                    $('#purchase_number').append('<option value="">--select--</option>');

                    response.forEach(function(purchaseNumber){
                        $('#purchase_number').append(
                            `<option value="${purchaseNumber.id}">${purchaseNumber.purchase_number}</option>`
                        );
                    });

                }
            }
        });
    }

    function getPurchaseItemQuantity(){
        var purchaseId = document.getElementById('purchase_number').value;
        if (purchaseId != '') {
            var itemId = $('#item_id').val();

            $.ajax({
                type: "POST",
                url: "{{ route('ajax.itempurchasequantity')}}",
                data: {
                    purchase_id : purchaseId,
                    item_id : itemId,
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);

                    if(response != ""){
                        $('#total_quantity').val(response);
                        $('#purchase_item_quantity').val(response);
                        $('#is_purchase').val(1);

                        totalBarcode();
                    }else{
                        $('purchase_item_quantity').val('');
                    }

                }
            });
        }
    }

    function getItemUOM(){
        var itemId = $('#item_id').val();

        $.ajax({
            type: "POST",
            url: "{{route('ajax.getitemuom')}}",
            data: {
                item_id : itemId
            },
            dataType: "json",
            success: function (response) {
                if (response) {

                    $('#uom').val('');
                    $('#spq').val('');

                    $('#spq').val(response.spq_quantity);
                    $('#uom').val(response.uom_name);

                    $('#total_quantity').val('');
                    $('#number_of_barcodes').val(1);

                    getPurchaseItemQuantity();

                }
            }
        });
    }

    function ifItem() {
        var item = $('#item_id').val();

        if (!item) {
            $('#total_quantity').val('');
            alert('Select item');
            return false;
        }

        return true;
    }

    function totalBarcode() {
        if (ifItem()) {
            var spq = parseFloat($('#spq').val());
            var totalQuantity = parseFloat($('#total_quantity').val());
            var purchaseQuantity = $('#purchase_item_quantity').val();

            if (isNaN(spq) || isNaN(totalQuantity)) {
                alert('Please enter valid numbers for SPQ and Total Quantity');
                return;
            }

            if (totalQuantity <= 0) {
                alert('Total Quantity must be greater than zero');
                return;
            }

            if(totalQuantity < spq){
                alert('Total Quantity must be greater than or equal to spq');
                $('#total_quantity').val(spq);
                return;
            }

            if (purchaseQuantity && !isNaN(purchaseQuantity) && totalQuantity > parseFloat(purchaseQuantity)) {
                alert('Total Quantity cannot be greater than purchase quantity');
                $('#total_quantity').val(purchaseQuantity);
                return;
            }

            var fullPacks = Math.floor(totalQuantity / spq);
            var remainder = totalQuantity % spq;
            var totalBarcodes = remainder > 0 ? fullPacks + 1 : fullPacks;

            $('#number_of_barcodes').val(totalBarcodes);
        }
    }

    function itemTotalQuantity() {
        if (ifItem()) {
            var spq = parseFloat($('#spq').val());
            var barcode = parseFloat($('#number_of_barcodes').val());
            var purchaseQuantity = $('#purchase_item_quantity').val();

            if (barcode <= 0) {
                alert('Number of Barcodes must be greater than zero');
                $('#number_of_barcodes').val('');
                return;
            }

            var totalQuantity = spq * barcode;

            if (purchaseQuantity && !isNaN(purchaseQuantity) && totalQuantity > parseFloat(purchaseQuantity)) {
                alert('Total Quantity cannot be greater than purchase quantity');
                $('#total_quantity').val(purchaseQuantity);
                return;
            }

            $('#total_quantity').val(totalQuantity.toFixed(0));
        }
    }

    // Function to add item to the grid
    function addToGrid() {

        // Retrieve form values
        let dateom = $('#dom').val();
        let bbv = $('#best_before_value').val();
        let itemId = $('#item_id').val();
        let itemName = $('#item_id option:selected').text();
        // let price = $.trim($("#price").val());
        let batchNo = $.trim($("#batch_no").val());
        let spq = $.trim($("#spq").val());
        let totalQuantity = $.trim($("#total_quantity").val());
        let ponumber = $.trim($("#purchase_number").val());
        let poQty = $.trim($("#po_qty").val());
        let numberOfBarcodes = $.trim($("#number_of_barcodes").val());
        let itemType = $.trim($("#grn_type").val());

        // itemTotalQuantity();

        // Validate if DOM is before Best Before date
        if (dateom > bbv) {
            alert("Date of Manufacture should be less than Best Before date");
            return;
        }

        // Validate required fields
        if (!itemId) {
            alert("Please select an item");
            return;
        } else if (!batchNo) {
            alert("Please enter batch number");
            return;
        }
        // else if (!price) {
        //     alert("Please enter Price number");
        //     return;
        // }
        else if (!dateom) {
            alert("Please enter DOM");
            return;
        } else if (!bbv) {
            alert("Please enter Best Before");
            return;
        } else if (!spq) {
            alert("Please enter SPQ");
            return;
        } else if (!numberOfBarcodes) {
            alert("Please enter the number of barcodes");
            return;
        } else if (!totalQuantity) {
            alert("Please enter the number of Total Quantity");
            return;
        }

        // Check for duplicates in grid
        if ($('#grngridbody').find('tr').filter(function() {
            return $(this).find('td').eq(1).text() === itemName && $(this).find('td').eq(3).text() === batchNo;
        }).length > 0) {
            alert("Item already added");
            return;
        }

        // Check PO quantity exceeds limit logic
        let totalPoQty = 0;
        $('#grngridbody tr').each(function() {
            let rowItemId = $(this).find('td').eq(1).text();
            let rowQty = $(this).find('td').eq(3).text();

            if (rowItemId === itemName) {
                totalPoQty += parseInt(rowQty);
            }
        });

        if (totalPoQty + parseInt(totalQuantity) > parseInt(poQty)) {
            alert("PO Quantity exceeded");
            return;
        }

        // Add the data to the grid
        let rowIndex = $('#grngridbody tr').length + 1; // Use row count as index

        $('#grngridbody').append(`
            <tr data-item-id="${itemId}">
                <td>${rowIndex}</td> <!-- Dynamic row number -->
                <td>${itemName}</td>
                <td>${batchNo}</td>
                <td>${dateom}</td>
                <td>${bbv}</td>
                <td>${spq}</td>
                <td>${totalQuantity}</td>
                <td>${numberOfBarcodes}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${itemId}, ${rowIndex})">Remove</button></td>
            </tr>
        `);

        // Add hidden inputs to form
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][item_id]`,
            value: itemId
        }).appendTo('form');

        // $('<input>').attr({
        //     type: 'hidden',
        //     name: `items[${rowIndex}][price]`,
        //     value: price
        // }).appendTo('form');

        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][dom]`,
            value: dateom
        }).appendTo('form');
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][bbf]`,
            value: bbv
        }).appendTo('form');
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][spq]`,
            value: spq
        }).appendTo('form');
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][total_quantity]`,
            value: totalQuantity
        }).appendTo('form');
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][number_of_barcodes]`,
            value: numberOfBarcodes
        }).appendTo('form');
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][purchase_id]`,
            value: ponumber
        }).appendTo('form');
        $('<input>').attr({
            type: 'hidden',
            name: `items[${rowIndex}][item_type]`,
            value: itemType
        }).appendTo('form');

        // Reset the form fields for the next entry
        $('#item_id').val('').trigger('change');
        $('#purchase_number').val('');
        // $('#price').val('');
        $('#dom').val('');
        $('#best_before_value').val('');
        $('#uom').val('');
        $('#spq').val('');
        $('#total_quantity').val('');
        $('#po_qty').val('');
        $('#number_of_barcodes').val('');
    }

    // Function to remove item from the grid
    function removeItem(itemId, rowIndex) {
        // Remove row from the grid
        $(`tr[data-item-id="${itemId}"]`).remove();

        // Remove hidden inputs associated with this item
        $(`input[name="items[${rowIndex}][item_id]"]`).remove();
        // $(`input[name="items[${rowIndex}][price]"]`).remove();
        $(`input[name="items[${rowIndex}][dom]"]`).remove();
        $(`input[name="items[${rowIndex}][bbf]"]`).remove();
        $(`input[name="items[${rowIndex}][spq]"]`).remove();
        $(`input[name="items[${rowIndex}][total_quantity]"]`).remove();
        $(`input[name="items[${rowIndex}][number_of_barcodes]"]`).remove();
        $(`input[name="items[${rowIndex}][purchase_id]"]`).remove();

        // Update the row numbers
        updateRowNumbers();
    }

    // Update the row numbers after removing an item
    function updateRowNumbers() {
        $('#grngridbody tr').each(function(index) {
            $(this).find('td').eq(0).text(index + 1);  // Update row number
        });
    }


    // Reset grid
    $('#reset').click(function() {
        rowReset();
    });

    function rowReset() {
        $('#grngridbody').empty();
        $('form input[type="hidden"]').remove();
        itemCount = 0;
    }

    $('#uploadModal form').on('submit', function() {
        $('#uploadButton').prop('disabled', true).text('Uploading...');
    });

    function checkData() {

        var grnType = document.getElementById('grn_type').value;
        var location = document.getElementById('location_id').value;
        var vendor = document.getElementById('vendor_id').value;
        var itemCount = document.getElementById('grngridbody').rows.length;
        console.log(itemCount);


        if(!grnType){
            alert('Please Select Grn Type');
            return false;
        }
        else if(!vendor){
            alert('Please Select Vendor');
            return false;
        }
        else if(!location){
            alert('Please Select Location');
            return false;
        }
        else if (itemCount == 0) {
            alert('Empty Grid: Please add items to the GRN.');
            return false;
        }
        else {
            document.getElementById('grnForm').submit();
        }
    }

</script>

{{-- @includeWhen(!is_null(session()->get('print_barcode')), 'print.barcodeprintpopup', ['print_barcode'=>session()->get('print_barcode')]) --}}
@if (!is_null(session()->get('contents')))
    <script>
        console.log('qwertyuiop');

        window.open("{{ route('printbarcode') }}");
    </script>
@endif
