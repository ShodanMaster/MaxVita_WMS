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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="btn-group" role="group" aria-label="Barcode Options">
                        <button type="button" class="btn btn-outline-primary active" onclick="toggleForm('with')">With Barcode</button>
                        <button type="button" class="btn btn-outline-primary" onclick="toggleForm('without')">Without Barcode</button>
                    </div>
                </div>
                <div class="card-body" id="with-barcode" style="display: block">
                    <div class="form-group row">
                        <label for="customer" class="col-sm-4 control-label">
                            Customer<font color="#FF0000" size="">*</font>
                        </label>
                        <div class="col-sm-8">
                            <select name="customer" id="customer" class="js-example-basic-single form-select2 mandatory">
                                <option value="" selected disabled>-- Select Customer</option>
                                @forelse ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @empty
                                    <option value="" disabled>No Customers Found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bin" class="col-sm-4 control-label">
                            Bin <font color="#FF0000">*</font>
                        </label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" id="bin" name="bin" class="form-control form-control-sm" required oninput="binExists()" value="{{ isset($bin) ? $bin : '' }}">
                                <button type="button" class="btn btn-sm" id="reset-button" title="Reset Bin" style="display: none" onclick="resetButton()">
					                <i class="link-icon" data-feather="refresh-ccw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-4 control-label">
                            Barcode<font color="#FF0000" size="">*</font>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="barcode" name="barcode" class="form-control form-control-sm" oninput="withBarcode()" required>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="without-barcode" style="display: none">

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
    function toggleForm(type) {
        const withBarcode = document.getElementById('with-barcode');
        const withoutBarcode = document.getElementById('without-barcode');
        const buttons = document.querySelectorAll('.btn-group .btn');

        buttons.forEach(btn => btn.classList.remove('active'));

        if (type === 'with') {
            withBarcode.style.display = 'block';
            withoutBarcode.style.display = 'none';
            buttons[0].classList.add('active');
        } else {
            withBarcode.style.display = 'none';
            withoutBarcode.style.display = 'block';
            buttons[1].classList.add('active');
        }
    }

    function binExists() {
        const bin = document.getElementById('bin');
        const binValue = bin.value.trim();
        const resetButton = document.getElementById('reset-button');
        const barcode = document.getElementById('barcode');

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.bin-exists') }}",
            data: {
                bin : binValue
            },
            dataType: "json",
            success: function (response) {
                console.log(response);

                if (response.status === 200) {
                    bin.readOnly = true;
                    resetButton.style.display = 'block';
                    barcode.focus();
                } else {
                    sweetAlertMessage('warning', 'Invalid Bin', 'Bin Code Not Found!');
                    bin.value = '';
                    bin.readOnly = false;
                    bin.focus();
                    resetButton.style.display = 'none';
                }
            }
        });
    }

    async function withBarcode() {
        let customer = document.getElementById('customer');
        let bin = document.getElementById('bin');
        let barcode = document.getElementById('barcode');

        if (customer.value === '') {
            sweetAlertMessage('warning', 'Select Customer', 'Please select customer!');
            customer.focus();
            return;
        }

        if (bin.value === '') {
            sweetAlertMessage('warning', 'Enter Bin', 'Please enter bin!');
            bin.focus();
            return;
        }

        if (barcode.value === '') {
            sweetAlertMessage('warning', 'Enter Barcode', 'Please enter barcode!');
            barcode.focus();
            return;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.with-barcode-data') }}",
            data: {
                customer_id: customer.value,
                bin: bin.value,
                barcode: barcode.value,
            },
            dataType: "json",
            success: function (response) {
                console.log(response);

                if (response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Details Found',
                        html: `
                            <b>Customer Name:</b> ${response.data.customer_name} <br>
                            <b>Dispatch Number:</b> ${response.data.dispatch_number} <br>
                            <b>Item Name:</b> ${response.data.item_name} <br><br>
                            <b>Would you like to return this item?</b>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Return',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let customer_id = response.data.input_data.customer_id;
                            let dispatch_id = response.data.input_data.dispatch_id;
                            let item_id = response.data.input_data.item_id;
                            let bin_id = response.data.input_data.bin_id;
                            let barcode_value = barcode.value;

                            returnItem(barcode_value, customer_id, dispatch_id, item_id, bin_id);
                        }
                    });
                } else {
                    sweetAlertMessage('error', 'Barcode not found');
                    barcode.value = '';
                    barcode.focus();
                }
            },
            error: function () {
                sweetAlertMessage('error', 'Server Error', 'Unable to process request.');
            }
        });
    }

    async function returnItem(barcode, customer_id, dispatch_id, item_id, bin_id) {
        $.ajax({
            type: "POST",
            url: "{{ route('ajax.item-return') }}",
            data: {
                barcode: barcode,
                customer_id: customer_id,
                dispatch_id: dispatch_id,
                item_id: item_id,
                bin_id: bin_id,
            },
            dataType: "json",
            success: function (response2) {
                if (response2.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Returned',
                        text: response2.message || 'Item successfully returned!'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response2.message || 'Unable to return item.'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Something went wrong while returning item.'
                });
            }
        });
    }

</script>
@endpush
