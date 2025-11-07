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
                        <label for="barcode" class="col-sm-4 control-label">
                            Barcode<font color="#FF0000" size="">*</font>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="barcode" name="barcode" class="form-control form-control-sm" oninput="stockOutScan()" required>
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

    
</script>
@endpush
