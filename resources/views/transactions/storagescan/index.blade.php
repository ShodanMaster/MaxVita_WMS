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
@include('messages')
<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Storage Scan</h3>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <form method="patch" action="" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label for="grn_no" class="col-md-4 control-label">
                                    GRN No
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="grn_no" value="" class="form-control form-control-sm" required readonly id="grn_no">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bin" class="col-sm-4 control-label">
                                    Bin <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="bin" value="" class="form-control form-control-sm" required onchange="checkbin(this)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="barcode" class="col-sm-4 control-label">
                                    Barcode <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <input type="hidden" id="scanned_barcodes" name="scanned_barcodes">
                                    <input type="text" name="barcode" value="" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Scan</button>

                        </form>
                    </div>
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
@endpush
