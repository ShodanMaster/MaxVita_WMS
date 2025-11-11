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
                    <h5 class="card-title">
                        Dispatch Edit
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="dispatch_number" class="col-md-4 control-label">
                            Dispatch No
                        </label>
                        <div class="col-sm-8">
                            <select name="dispatch_number" id="dispatch_number" class="form-control form-control-sm select2" required onchange="fetchDispatchDetails()">
                                <option value="" disabled selected>--Select Dispatch Number--</option>
                                @forelse ($dispatchNumbers as $dispatchNumber)
                                    <option value="{{ $dispatchNumber->id }}">{{ $dispatchNumber->dispatch_number }}</option>
                                @empty
                                    <option value="" disabled >No Dispatch Numbers Found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Edit</button>
                </div>
                <div class="card-footer table-responsive" style="display: none" id="dispatchDetails">
                    <table class="table table-bordered mb-3">
                        <tbody>
                            <tr>
                                <td><strong>Dispatch Number:</strong></td>
                                <td id="disp_number"></td>
                                <td><strong>Dispatch Date:</strong></td>
                                <td id="disp_date"></td>
                            </tr>
                            <tr>
                                <td><strong>From Branch:</strong></td>
                                <td id="disp_branch"></td>
                                <td><strong>From Location:</strong></td>
                                <td id="disp_location"></td>
                            </tr>
                            <tr>
                                <td><strong>Dispatch To:</strong></td>
                                <td id="disp_to"></td>
                                <td><strong>Dispatch Type:</strong></td>
                                <td id="disp_type"></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table" id="dispatchGrid">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>UOM</th>
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

    function fetchDispatchDetails() {
        var dispatchNumber = document.getElementById("dispatch_number").value;

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.get-dispatch-details') }}",
            data: {
                id: dispatchNumber,
                _token: '{{ csrf_token() }}' // include CSRF token
            },
            dataType: "json",
            success: function(response) {
                console.log(response);

                if (response && Object.keys(response).length > 0) {
                    // Show the container
                    document.getElementById("dispatchDetails").style.display = "block";

                    // Populate dispatch info
                    document.getElementById("disp_number").textContent = response.dispatch_number || '';
                    document.getElementById("disp_date").textContent = response.dispatch_date || '';
                    document.getElementById("disp_branch").textContent = response.from_branch || '';
                    document.getElementById("disp_location").textContent = response.from_location || '';
                    document.getElementById("disp_to").textContent = response.dispatch_to || '';
                    document.getElementById("disp_type").textContent = response.dispatch_type || '';

                    // Populate items table
                    const tbody = document.getElementById("grid-container");
                    tbody.innerHTML = "";

                    if (Array.isArray(response.items) && response.items.length > 0) {
                        response.items.forEach(item => {
                            const tr = document.createElement("tr");
                            tr.innerHTML = `
                                <td>${item.item || ''}</td>
                                <td>${item.total_quantity ?? ''}</td>
                                <td>${item.uom ?? ''}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                } else {
                    // Hide container if no data
                    document.getElementById("dispatchDetails").style.display = "none";
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Failed to fetch dispatch details.");
            }
        });
    }

    function submitForm(){
        dispatchNumber = document.getElementById('dispatch_number').value;

        if(!dispatchNumber){
            sweetAlertMessage('warning', 'Select Dispatch Number', 'Please select dispatch number!');
            return
        }
        const editUrl = `{{ route('dispatch.edit', ':id') }}`.replace(':id', dispatchNumber);
        window.location.href = editUrl;
    }
</script>
@endpush
