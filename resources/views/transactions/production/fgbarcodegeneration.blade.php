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
                <div class="card-body">
                    <form method="POST" action="{{ route('fg-barcode-generation.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="plan_number" class="col-md-4 control-label">
                                Production Plan No
                            </label>
                            <div class="col-sm-8">
                                <select name="plan_number" id="plan_number" class="form-control form-control-sm select2" required onchange="fetchProductionDetails()">
                                    <option value="" disabled selected>--Select Plan Number--</option>
                                    @forelse ($planNumbers as $planNumber)
                                        <option value="{{ $planNumber->plan_number }}">{{ $planNumber->plan_number }}</option>
                                    @empty
                                        <option value="" disabled >No Plan Numbers Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Start</button>

                    </form>
                </div>
                <div class="card-footer table-responsive" style="display: none" id="productionDetails">
                    <table class="table" id="grngrid">
                        <thead>
                            <tr>
                                <th>Plan No</th>
                                <th>Item</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="grid-container">
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
    $('.select2').select2();

    function fetchProductionDetails(){
        var planNumber = document.getElementById("plan_number").value;
        console.log(planNumber);

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getproductiondetails') }}",
            data: {
                plan_number : planNumber
            },
            dataType: "json",
            success: function (response) {
                const tbody = document.getElementById("grid-container");
                tbody.innerHTML = "";

                if (Array.isArray(response) && response.length > 0) {
                    document.getElementById("productionDetails").style.display = "block";

                    response.forEach(item => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${item.plan_number ?? ''}</td>
                            <td>${item.item ?? ''}</td>
                            <td>${item.total_quantity ?? ''}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    document.getElementById("productionDetails").style.display = "none";
                }
            }
        });
    }
</script>
@endpush
