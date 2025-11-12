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
                <div class="card-header">
                    <h5 class="card-title">
                        Purchase Edit
                    </h5>
                </div>
                <div class="card-body">
                    <form id="purchaseOrder" action="{{ route('purchase-order.update', $purchaseOrder->id)}}" method="POST">
                        @csrf
                        @method('PATCH')
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
                                        value="{{ $purchaseOrder->purchase_number }}"
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
                                        value="{{ $purchaseOrder->purchase_date }}"
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
                                            <option value="{{ $vendor->id }}" {{ $purchaseOrder->vendor_id == $vendor->id ? 'selected' : '' }}>
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
                                    @foreach ($purchaseOrder->purchaseOrderSubs as $purchaseOrderSub)
                                        <tr data-item-id="{{ $purchaseOrderSub->item->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $purchaseOrderSub->item->item_code }}/{{ $purchaseOrderSub->item->name }}</td>
                                            <td>{{ $purchaseOrderSub->item->spq_quantity }}</td>
                                            <td>{{ $purchaseOrderSub->quantity }}</td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>

                                            <input type="hidden" name="items[{{ $loop->iteration }}][item_id]" value="{{ $purchaseOrderSub->item_id }}" data-row="{{ $loop->iteration }}">
                                            <input type="hidden" name="items[{{ $loop->iteration }}][spq]" value="{{ $purchaseOrderSub->item->spq_quantity }}" data-row="{{ $loop->iteration }}">
                                            <input type="hidden" name="items[{{ $loop->iteration }}][total_quantity]" value="{{ $purchaseOrderSub->quantity }}" data-row="{{ $loop->iteration }}">
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="button" class="btn btn-primary" id="submitButton">
                                Update
                            </button>
                            <input type="reset" class="btn btn-default" value="Clear" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
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

        let itemCount = $('#grngridbody tr').length;

        $('.select2').select2();

        $('#total_quantity').change(function() {
            checkSpq();
        });

        $('#submitButton').click(function() {
            checkData();
        });


        function checkData() {
            if ($('#purchase_number').val() == '') {
                sweetAlertMessage('warning', 'Enter PO Number', 'Please enter PO number!');
                return false;
            }

            if ($('#purchase_date').val() == '') {
                sweetAlertMessage('warning', 'Select Purchase Date', 'Please select purchase date!');
                return false;
            }

            if ($('#vendor').val() == '') {
                sweetAlertMessage('warning', 'Select Vendor', 'Please select vendor!');
                return false;
            }


            if (itemCount == 0) {
                sweetAlertMessage('warning', 'Empty Grid', 'Please add items to grid!');
                return false;
            } else {
                $("#purchaseOrder").submit();
            }
        }

        function checkSpq() {
            var qty=document.getElementById('total_quantity').value;
            var spq=document.getElementById('spq').value;
            if((Number(qty)%Number(spq)) != '0'){

                sweetAlertMessage('warning', 'Invalid Quantity', 'Quantity Not Multiple of Spq!');
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
                sweetAlertMessage('warning', 'Fill The Fields', 'Please fill out all fields before adding to the grid!');
                return;
            }

            if ($('#grngridbody').find(`tr[data-item-id="${itemId}"]`).length > 0) {
                sweetAlertMessage('warning', 'Already Exists', 'This item has already been added!');
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
