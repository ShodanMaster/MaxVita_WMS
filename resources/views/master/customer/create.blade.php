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




<style>
    #prifix {
        text-transform: uppercase;
    }
</style>

@endpush
@section('content')
<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        @if (isset($customer))
                        Update Customer
                        @else
                        Add Customer
                        @endif
                    </h5>

                    <form
                        action="{{ isset($customer) ? route('customer.update', $customer->id) : route('customer.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off">
                        @csrf
                        @if (isset($customer))
                        @method('PATCH')
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        Customer Name <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('name', $customer->name ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="customer_code" class="col-sm-4 control-label">
                                        Customer Code <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="customer_code"
                                        id="customer_code"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('customer_code', $customer->customer_code ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="shipping_code" class="col-sm-4 control-label">
                                        Shipping Code
                                    </label>
                                    <input
                                        type="text"
                                        name="shipping_code"
                                        id="shipping_code"
                                        class="form-control form-control-sm"
                                        value="{{ old('shipping_code', $customer->shipping_code ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="customer_address" class="col-sm-4 control-label">
                                        Customer Address <font color="#FF0000">*</font>
                                    </label>
                                    <textarea
                                        name="customer_address"
                                        class="form-control form-control-sm"
                                        rows="4"
                                        required>{{ old('customer_address', $customer->customer_address ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="zip_code" class="col-sm-4 control-label">
                                        Zip Code
                                    </label>
                                    <input
                                        type="text"
                                        name="zip_code"
                                        id="zip_code"
                                        class="form-control form-control-sm"
                                        value="{{ old('zip_code', $customer->zip_code ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="gst_number" class="col-sm-4 control-label">
                                        GST Registration Number <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="gst_number"
                                        id="gst_number"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('gst_number', $customer->gst_number ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($customer) ? 'Update' : 'Add' }}
                            </button>
                            <input type="reset" class="btn btn-default" value="Clear" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>



<script>


</script>


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


@endpush
