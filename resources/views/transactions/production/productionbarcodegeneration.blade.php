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
                    <h3 class="card-title">Production Barcode Generation</h3>
                </div>
                <form action="{{ route('production-barcode-generation.store') }}", method="POST" onsubmit="return check()">
                    @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="plan_number" class="col-md-4 control-label">
                            Production Plan No
                        </label>
                        <div class="col-sm-8">
                            <select name="plan_number" id="plan_number" class="form-control form-control-sm select2" required onchange="fetchPlanDetails()">
                                <option value="" disabled selected>--Select Plan Number--</option>
                                @forelse ($planNumbers as $planNumber)
                                    <option value="{{ $planNumber->id }}">{{ $planNumber->plan_number }}</option>
                                @empty
                                    <option value="" disabled >No Plan Numbers Found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="display: none" id="planDetails">
                <div class="card-header d-flex justify-content-around">
                    <h3 class="card-title" id="fg-item"></h3>
                    <div>
                        <h3 class="card-title" id="item-quantity"></h3><br>
                    </div>
                    <div>
                        <h3 class="card-title" id="balance-quantity"></h3>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Batch: {{ $batch }}</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="dom" class="col-sm-4 control-label">DOM <font color="#FF0000">*</font></label>
                                <div class="col-sm-8">
                                    <input type="date" id="dom" name="date_of_manufacture" class="form-control form-control-sm mandatory" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="best_before_value" class="col-sm-4 control-label">Best Before <font color="#FF0000">*</font></label>
                                <div class="col-sm-8">
                                    <input type="date" name="best_before_date" id="best_before_value" class="form-control form-control-sm mandatory expiry-date" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="number_of_barcode" class="col-sm-4 control-label">Number of Barcode to Print <font color="#FF0000">*</font></label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="number_of_barcode" min="1" name="number_of_barcodes" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="uom" class="col-sm-4 control-label">UOM <font color="#FF0000">*</font></label>
                                <div class="col-sm-8">
                                    <select name="uom" id="uom" class="form-group select2" required>
                                        <option value="" disabled selected>--Select UOM--</option>
                                        @foreach ($uoms as $uom)
                                            <option value="{{ $uom->id }}">{{ $uom->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                        <input type="hidden" id="balance-quantity-input" name="balance-quantity-input">

                    <div class="d-flex justify-content-between">
                        <div class="form-inline">
                            <label for="prn" class="control-label">PRN Print</label>
                            <input type="checkbox" name="prn" id="prn" class="custom-class ml-2">
                        </div>

                        <button type="submit" class="btn btn-primary" onclick="check()">Submit</button>
                    </div>
                </div>
                </form>
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
    $('.select2').select2();

    function fetchPlanDetails(){
        var planNumber = document.getElementById("plan_number").value;
        console.log(planNumber);

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getplandetails') }}",
            data: {
                plan_number : planNumber
            },
            dataType: "json",
            success: function (response) {
                const fgItem = document.getElementById("fg-item");
                const totalQuanity = document.getElementById("item-quantity");
                const balanceQuanity = document.getElementById("balance-quantity");
                const balanceQuanityInput = document.getElementById("balance-quantity-input");

                if (response) {
                    document.getElementById("planDetails").style.display = "block";

                    fgItem.innerText = response.fg_item;
                    totalQuanity.innerText = "Total Quantity: " +  response.total_quantity;
                    balanceQuanity.innerText = "Balance Quantity: " +  response.balance_quantity;
                    balanceQuanityInput.value = response.balance_quantity;

                } else {
                    document.getElementById("planDetails").style.display = "none";
                }
            }
        });
    }

    function check(){
        const balanceQuantityInput = document.getElementById("balance-quantity-input");
        const numberOfBarcodeInput = document.getElementById("number_of_barcode");

        const balanceQuantity = parseFloat(balanceQuantityInput.value) || 0;
        const numberOfBarcode = parseFloat(numberOfBarcodeInput.value) || 0;

        if(numberOfBarcode > balanceQuantity){
            sweetAlertMessage('warning', 'Quantity Exceeded', 'Number of barcode Quantity can not be grater than Balance Quantity');
            numberOfBarcodeInput.value = '';
            return false;
        }
        return true;
    }
</script>
@endpush

@if (!is_null(session()->get('contents')))
    <script>
        console.log("qwertyu: ", @json(session()->all()));

        window.open("{{ route('printbarcode') }}");
    </script>
@endif
