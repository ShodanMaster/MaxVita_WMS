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
                    <h3 class="card-title">Production Issue Scan</h3>
                </div>
                <div class="card-body">
                    @csrf
                    <div class="form-group row">
                        <label for="plan_number" class="col-md-4 control-label">Plan No</label>
                        <div class="col-sm-8">
                            <input type="text" id="plan_number" name="plan_number" class="form-control form-control-sm" required readonly value="{{ $productionPlan->plan_number }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="weight" class="col-sm-4 control-label">
                            Weight <font color="#FF0000">*</font>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="weight" name="weight" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-4 control-label">
                            Barcode <font color="#FF0000">*</font>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="barcode" name="barcode" class="form-control form-control-sm" oninput="productionIssueScan()" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#detailsModal">
                        View Details
                    </button>
                </div>
            </div>
            <div class="card mt-2" id="barcode-card" style="display: none">
                <div class="card-body" style="overflow-x: auto;overflow-y:auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Weight</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody id="dataTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">FG Item: {{$productionPlan->item->item_code}}/{{$productionPlan->item->name}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>RM Item</th>
                            <th>Total Quantity</th>
                            <th>Picked Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="balancegrid">
                        @foreach ($productionPlan->productionPlanSubs as $productionPlanSub)
                            <tr>
                                <td>{{$productionPlanSub->item->name}}</td>
                                <td>{{$productionPlanSub->total_quantity}}</td>
                                <td>{{$productionPlanSub->picked_quantity}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

<script>

    function productionIssueScan(){
        const planNumber = document.getElementById('plan_number');
        const weight = document.getElementById('weight');
        const barcode = document.getElementById('barcode');

        if(weight.value ==''){
            sweetAlertMessage('warning', 'Enter Weight', 'You must enter weight!');
            weight.focus();
            barcode.value = '';
            return false;
        }

        if(planNumber.value == ''){
            sweetAlertMessage('warning', 'Invalid Plan Number', 'Plan Number Not Found!');
            window.location.href = "{{ route('production-issue.index') }}";
            return false;
        }

        if(barcode.value ==''){
            sweetAlertMessage('warning', 'Enter Barcode', 'You must enter barcode!');
            barcode.focus();
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.productionissuescan') }}",
            data: {
                production_plan_id : {{ $id }},
                weight : weight.value,
                barcode : barcode.value,
            },
            dataType: "json",
            success: function (response) {
                document.getElementById('barcode-card').style.display = 'block';
                if(response){
                    var row = $('<tr>');

                    row.append('<td>' + barcode.value + '</td>');
                    row.append('<td>' + weight.value + '</td>');

                    var messageCell = $('<td>').text(response.message);

                    if (response.status === 200) {
                        messageCell.css('color', 'green');
                    } else {
                        messageCell.css('color', 'red');
                    }

                    row.append(messageCell);
                    $('#dataTable').prepend(row);

                    if(response.scan_complete){
                        sweetAlertMessage('success', 'Success', 'Production Issue Scan Completed!', false, "{{ route('production-issue.index') }}");
                    }
                }
                weight.value = '';
                barcode.value = '';
            }
        });
    }
</script>

@endpush
