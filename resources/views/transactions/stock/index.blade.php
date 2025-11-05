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
                <div class="card-header mt-2">
                    <h3 class="card-title">Opening Stock Excel Upload</h3>
                </div>
                <form action="{{ route('opening-stock-excel-upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="custom-file">
                                        <input type="file" name="excel_file" class="form-control mb-2" required>
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="mt-2">
                                            You can download the excel in the predefined format by <a href="{{ URL::to( '/excel_templates/transaction_templates/opening_stock_template.xlsx')}}" class="text-primary ">Clicking here</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <input type="reset" class="btn btn-default" value="Clear">
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header mt-2">
                    <h3 class="card-title">Generate Barcode</h3>
                </div>
                <form action="{{ route('opening-stock.store') }}" method="POST" enctype="multipart/form-data" id="openingstock">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Opening Number -->
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="opening_number" class="col-sm-4 control-label">
                                        Opening Number <font color="#FF0000">*</font>
                                    </label>

                                    <div class="col-sm-8">
                                        <select name="opening_number" id="opening_number" class="js-example-basic-single form-control"
                                            style="width:100%" required onchange="getItem()">
                                            <option value="">--select--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Item -->
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="item" class="col-sm-4 control-label">Item</label>

                                    <div class="col-sm-8">
                                        <select name="item" id="item" class="js-example-basic-single form-control">
                                            <option value="">--select--</option>
                                            <!-- Options will be populated dynamically via JS -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input type="reset" class="btn btn-default" value="Clear">
                    </div>
                </form>
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
