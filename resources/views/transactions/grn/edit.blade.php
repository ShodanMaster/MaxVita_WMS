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
                        GRN Edit
                    </h5>

                    <select name="grn_number" id="grn_number" class="js-example-basic-single form-control mandatory">
                        <option value="">--select grn number--</option>
                        @forelse ($grnNumbers as $grnNumber)
                            <option value=""></option>
                        @empty

                        @endforelse
                    </select>
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
                                        <select name="vendor_id" id="vendor_id" class="js-example-basic-single form-control mandatory" onchange="getPurchaseNumber();">
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
                                        <select name="location_id" id="location_id" class="js-example-basic-single form-select mandatory" required>
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
                                        <input type="checkbox" name="prn" id="prn" class="custom-class" value="1">
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
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="price" class="col-sm-4 control-label">Price <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="price" class="form-control form-control-sm mandatory"" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="dom" class="col-sm-4 control-label">DOM <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="date" id="dom" class="form-control form-control-sm mandatory" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="best_before_value" class="col-sm-4 control-label">Best Before <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="date" id="best_before_value" class="form-control form-control-sm mandatory expiry-date" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="spq" class="col-sm-4 control-label">SPQ <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="spq" class="form-control form-control-sm mandatory" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="total_quantity" class="col-sm-4 control-label">Total Quantity <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="total_quantity" class="form-control form-control-sm mandatory" onkeyup="totalBarcode()" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="number_of_barcodes" class="col-sm-4 control-label">Number Of Barcodes <font color="#FF0000">*</font></label>
                                        <div class="col-sm-8">
                                            <input type="text" id="number_of_barcodes" class="form-control form-control-sm mandatory" onkeyup="totalQuantity()" pattern="^[1-9][0-9]*$" value="1" />
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                        <th>price</th>
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
