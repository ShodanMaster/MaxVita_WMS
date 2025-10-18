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
@include('messages')

<div class="container">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">
                            Purchase Order Entry
                        </h5>

                        <!-- Button trigger modal -->
                        <button type="button" class="btn" data-toggle="modal" data-target="#uploadModal">
                            <i data-feather="upload" class="text-primary" style="font-size: 24px;"></i><b> Upload </b>
                        </button>

                    </div>
                    <form id="purchaseOrder" action="{{ route('purchase-order.store')}}" method="POST">
                        @csrf

                        <!-- User Form Fields -->
                        <div class="row">
                            {{-- <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        PO Number
                                    </label>
                                    <input
                                        type="text"
                                        disabled
                                        class="form-control form-control-sm"
                                        value="{{ $purchaseNumber }}"
                                    >
                                </div>
                            </div> --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="purchase_number" class="col-sm-4 control-label">
                                        PO Number
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        id="purchase_number"
                                        name="purchase_number"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="purchase_date" class="col-sm-4 control-label">
                                        Purchase Date <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="date"
                                        name="purchase_date"
                                        id="purchase_date"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('purchase_date') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- User Name & Address -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="item" class="form-label">
                                        Item Code/Item Description <font color="#FF0000">*</font>
                                    </label>
                                    <select id="item" name="item" class="form-control select2 form-control-sm">
                                        <option value="" selected disabled>-- select --</option>
                                        @forelse ($items as $item)
                                            <option value="{{$item->id}}">
                                                {{$item->item_code}}/{{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No Items Available</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="spq_quantity" class="col-sm-4 control-label">
                                        SPQ Quantity
                                    </label>
                                    <input
                                        type="text"
                                        name="spq_quantity"
                                        id="spq_quantity"
                                        readonly
                                        class="form-control form-control-sm"
                                        value=""
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="total_quantity" class="col-sm-4 control-label">
                                        Total Quantity <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="number"
                                        name="total_quantity"
                                        id="total_quantity"
                                        class="form-control form-control-sm"
                                        value="{{ old('total_quantity') }}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="vendor" class="col-sm-4 control-label">
                                        Vendor <font color="#FF0000">*</font>
                                    </label>
                                    <select id="vendor" name="vendor" class="form-control select2 form-control-sm">
                                        <option value="" selected disabled>-- select --</option>
                                        @forelse ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No Vendors Available</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <input type="button" value="ADD TO GRID" class="btn btn-primary" id="addtogrid"/>
                            &nbsp;
                            <input type="button" name="reset" id="reset" class="btn btn-default" value="Reset" onclick="rowReset()" />
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="grngrid">
                                <thead>
                                    <tr>
                                        <th>Sl.no</th>
                                        <th>Part No/Item Name</th>
                                        <th>SPQ</th>
                                        <th>Total Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="grngridbody">
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="button" class="btn btn-primary" id="submitButton">
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
                <h5 class="modal-title" id="uploadModalLabel">Purchase Order Excel Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('purchase-order-excel-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excelFile" class="form-label"></label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <span class="mt-2">You can download excel in predefined format by <a href="{{ URL::to( '/excel_templates/transaction_templates/purchase_order_template.xlsx')}}" class="text-primary ">Clicking Here</a></span>
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

        let itemCount = 0;

        $('.select2').select2();

        $('#total_quantity').change(function() {
            checkSpq();
        });

        $('#submitButton').click(function() {
            checkData();
        });


        function checkData() {
            if ($('#purchase_number').val() == '') {
                alert('Enter PO Number');
                return false;
            }

            if ($('#purchase_date').val() == '') {
                alert('Select Purchase Date');
                return false;
            }

            if ($('#vendor').val() == '') {
                alert('Select Vendor');
                return false;
            }


            if (itemCount == 0) {
                alert('Empty Grid');
                return false;
            } else {
                $("#purchaseOrder").submit();
            }
        }

        function checkSpq() {
            var qty=document.getElementById('total_quantity').value;
            var spq=document.getElementById('spq').value;
            if((Number(qty)%Number(spq)) != '0'){

                alert('Quantity Not Multiple of Spq!');
                document.getElementById('total_quantity').value='';
                document.getElementById('total_quantity').focus();
                return false;
            }

        }

        $('#item').on('change', function (e) {
            e.preventDefault();

            // Get the selected item ID
            var itemId = $(this).val();

            $.ajax({
                type: "POST",
                url: "{{ route('ajax.getspqquantity') }}",
                data: {
                    item_id: itemId
                },
                dataType: "json",
                success: function (response) {
                    if (response.spq_quantity) {
                        $('#spq_quantity').val(response.spq_quantity);
                    } else {
                        console.log("SPQ Quantity not found for this item.");
                    }
                },
                error: function (xhr, status, error) {
                    console.log("An error occurred: " + error);
                }
            });
        });

        $('#addtogrid').click(function() {
            let itemId = $('#item').val();
            let itemName = $('#item option:selected').text();
            let spq = $('#spq_quantity').val();
            let totalQuantity = $('#total_quantity').val();

            if (!itemId || !totalQuantity) {
                alert('Please fill out all fields before adding to the grid.');
                return;
            }

            if ($('#grngridbody').find('tr').filter(function() {
                return $(this).find('td').eq(1).text() === itemName;
            }).length > 0) {
                alert('This item has already been added.');
                return;
            }

            itemCount++;

            $('#grngridbody').append(`
                <tr data-item-id="${itemId}">
                    <td>${itemCount}</td>
                    <td>${itemName}</td>
                    <td>${spq}</td>
                    <td>${totalQuantity}</td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                </tr>
            `);

            $('<input>').attr({
                type: 'hidden',
                name: `items[${itemCount}][item_id]`,
                value: itemId
            }).appendTo('form');
            $('<input>').attr({
                type: 'hidden',
                name: `items[${itemCount}][spq]`,
                value: spq
            }).appendTo('form');
            $('<input>').attr({
                type: 'hidden',
                name: `items[${itemCount}][total_quantity]`,
                value: totalQuantity
            }).appendTo('form');

            $('#item').val('').trigger('change');
            $('#spq_quantity').val('');
            $('#total_quantity').val('');
        });

        $(document).on('click', '.remove-item', function() {

            let itemId = $(this).closest('tr').data('item-id');
            $(this).closest('tr').remove();

            $(`input[name="items[][item_id]"][value="${itemId}"]`).remove();
            $(`input[name="items[][spq]"][value="${itemId}"]`).remove();
            $(`input[name="items[][total_quantity]"][value="${itemId}"]`).remove();

            itemCount--;
            updateRowNumbers();
        });

        function updateRowNumbers() {
            $('#grngridbody tr').each(function(index) {
                $(this).find('td').eq(0).text(index + 1);
            });
        }

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

    });
</script>
@endpush
