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
                    <h3 class="card-title">Fg Barcode Generation</h3>
                </div>
                <form action="{{ route('fg-barcode-generation.store') }}", method="POST">
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
                <div class="card-header">
                    <h3 class="card-title">Product Details</h3>
                </div>
                <div class="card-body">

                    <div class="form-group row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="name" class="col-sm-4 control-label">
                                    FG Item
                                </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control form-control-sm"
                                    value=""
                                    id="fg_item"
                                >
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="purchase_date" class="col-sm-4 control-label">
                                    Total Quantity <font color="#FF0000">*</font>
                                </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control form-control-sm"
                                    value=""
                                    id="total_quantity"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-group row">
                        <label for="batch" class="col-md-4 control-label">
                            Batch
                        </label>
                        <div class="col-sm-8">
                            <input
                                    type="text"
                                    readonly
                                    class="form-control form-control-sm"
                                    value="{{ $batch }}"
                                    id="batch"
                                >
                        </div>
                    </div>
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
                                    <input type="date" name="best_before_date" id="best_before_value" class="form-control form-control-sm mandatory date-field" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
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
                const fgItem = document.getElementById("fg_item");
                const totalQuanity = document.getElementById("total_quantity");
                fgItem.value= "";
                totalQuanity.value= "";

                if (response) {
                    document.getElementById("planDetails").style.display = "block";

                    fgItem.value = response.fg_item;
                    totalQuanity.value = response.total_quantity;

                } else {
                    document.getElementById("planDetails").style.display = "none";
                }
            }
        });
    }

    function submitWeight(){
        var planNumber = document.getElementById("plan_number").value;
        const weight = document.getElementById('weight').value;

        if(weight == ""){
            alert("Enter Weight!");
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.fgbarcodegenerate') }}",
            data: {
                plan_number : planNumber,
                weight : weight
            },
            dataType: "json",
            success: function (response) {
                if(response){

                }
            }
        });

    }
</script>
@endpush
