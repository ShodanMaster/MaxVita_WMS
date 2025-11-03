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
<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Order Cancel</h3>
                </div>

                <form action="{{route('cancel-purchase-order')}}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="col-6">
                            <div class="form-group row">
                                <label for="purchase_order_id" class="col-sm-4 control-label">
                                    Purchase Order
                                    <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <select id="purchase_order_id" name="purchaseOrderNumber" class="js-example-basic-single form-select mandatory" style="width:100%" required>
                                        <option value="">-- Select PurchaseOrder --</option>
                                        @foreach($orders as $order)
                                        <option value="{{ $order->id }}">{{$order->purchase_number}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <button type="submit" class="btn btn-danger">Cancel</button>
                    </div><br>
                </form>

                <div class="table-responsive">
                    <table class="table responsive" id="grngrid">
                        <thead>
                            <tr>
                                <th>Sl.no</th>
                                <th>PART NO</th>
                                <th>ITEM NAME</th>
                                <th>SPQ</th>
                                <th>TOTAL QUANTITY</th>
                            </tr>
                        </thead>
                        <tbody id="purchasegridbody">
                            <!-- Table body will be dynamically populated by your server-side logic -->
                        </tbody>
                    </table>
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
    $('#purchase_order_id').on('change', function(e) {
        e.preventDefault();

        var purchaseNumber = $(this).val();
        console.log('changed');

        $.ajax({
            type: "post",
            url: "{{ route('ajax.getpurchaseorder')}}",
            data: {
                purchase_id: purchaseNumber
            },
            dataType: "json",
            success: function(response) {
                console.log(response)
                var tableBody = $('#purchasegridbody');
                tableBody.empty();

                response.forEach(function(item, index) {
                    var row = `<tr>
                    <td>${index + 1}</td> <!-- SL No. -->
                    <td>${item.item_code}</td>
                    <td>${item.item_name}</td>
                    <td>${item.spq_quantity ?? ''}</td>
                    <td>${item.quantity}</td>
                </tr>`;
                    tableBody.append(row);
                });
            }
        });
    });
</script>
@endpush
