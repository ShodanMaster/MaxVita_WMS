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
                    <h3 class="card-title">Storage Scan</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('storage-scan.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="grn_number" class="col-md-4 control-label">
                                GRN No
                            </label>
                            <div class="col-sm-8">
                                <select name="grn_number" id="grn_number" class="form-control form-control-sm select2" required onchange="fetchGrnDetails()">
                                    <option value="" disabled selected>--Select Grn Number--</option>
                                    @forelse ($grnNumbers as $grnNumber)
                                        <option value="{{ $grnNumber->id }}">{{ $grnNumber->grn_number }}</option>
                                    @empty
                                        <option value="" disabled >No Grn Numbers Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Start</button>

                    </form>
                </div>
                <div class="card-footer table-responsive" style="display: none" id="grnDetails">
                    <table class="table" id="grngrid">
                        <thead>
                            <tr>
                                <th>GRN No</th>
                                <th>Vendor Name</th>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Remark</th>
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
    function fetchGrnDetails(){
        var grnNumber = document.getElementById("grn_number").value;
        console.log(grnNumber);

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getgrndetails') }}",
            data: {
                id : grnNumber
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                const tbody = document.getElementById("grid-container");
                tbody.innerHTML = "";
                if(response && Object.keys(response).length > 0){
                    // Display the table footer
                    document.getElementById("grnDetails").style.display = "block";

                    // Create and insert new row
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${response.grn_number ?? ''}</td>
                        <td>${response.vendor_name ?? ''}</td>
                        <td>${response.invoice_number ?? ''}</td>
                        <td>${response.invoice_date ?? ''}</td>
                        <td>${response.remark ?? ''}</td>
                    `;
                    tbody.appendChild(tr);
                } else {
                    // Hide if no valid response
                    document.getElementById("grnDetails").style.display = "none";
                }
            }
        });
    }
</script>
@endpush
