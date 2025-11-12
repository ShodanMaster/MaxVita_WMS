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
                        Purchase Edit
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="purchase_number" class="col-md-4 control-label">
                            Purchase No
                        </label>
                        <div class="col-sm-8">
                            <select name="purchase_number" id="purchase_number" class="form-control form-control-sm select2" required onchange="fetchPurchaseDetails()">
                                <option value="" disabled selected>--Select Purchase Number--</option>
                                @forelse ($purchaseNumbers as $purchaseNumber)
                                    <option value="{{ $purchaseNumber->id }}">{{ $purchaseNumber->purchase_number }}</option>
                                @empty
                                    <option value="" disabled >No Purchase Numbers Found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Edit</button>
                </div>
                <div class="card-footer table-responsive" style="display: none" id="purchaseDetails">
                    <table class="table table-bordered mb-3">
                        <tbody>
                            <tr>
                                <td><strong>Purchase Number:</strong></td>
                                <td id="purchase_number"></td>
                                <td><strong>Purchase Date:</strong></td>
                                <td id="purchase_date"></td>
                            </tr>
                            <tr>
                                <td><strong>From Branch:</strong></td>
                                <td id="purchase_branch"></td>
                                <td><strong>From Location:</strong></td>
                                <td id="purchase_location"></td>
                            </tr>
                            <tr>
                                <td><strong>Purchase To:</strong></td>
                                <td id="purchase_to"></td>
                                <td><strong>Purchase Type:</strong></td>
                                <td id="purchase_type"></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table" id="purchaseGrid">
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

    function fetchPurchaseDetails() {
        var purchasehNumber = document.getElementById("purchase_number").value;

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.get-dispatch-details') }}",
            data: {
                id: purchasehNumber,
            },
            dataType: "json",
            success: function(response) {
                console.log(response);

                if (response && Object.keys(response).length > 0) {
                    // Show the container
                    document.getElementById("purchaseDetails").style.display = "block";

                    // Populate purchase info
                    document.getElementById("purchase_number").textContent = response.purchase_number || '';
                    document.getElementById("purchase_date").textContent = response.purchase_date || '';
                    document.getElementById("purchase_branch").textContent = response.from_branch || '';
                    document.getElementById("purchase_location").textContent = response.from_location || '';
                    document.getElementById("purchase_to").textContent = response.purchase_to || '';
                    document.getElementById("purchase_type").textContent = response.purchase_type || '';

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
                    document.getElementById("purchaseDetails").style.display = "none";
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Failed to fetch purchase details.");
            }
        });
    }

    function submitForm(){
        purchasehNumber = document.getElementById('purchase_number').value;

        if(!purchasehNumber){
            sweetAlertMessage('warning', 'Select Purchase Number', 'Please select purchase number!');
            return
        }
        const editUrl = `{{ route('purchase-order.edit', ':id') }}`.replace(':id', purchasehNumber);
        window.location.href = editUrl;
    }
</script>
@endpush
