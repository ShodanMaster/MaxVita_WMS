@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
@include('sweetalert::alert')
@include('messages')



<div class="card">
    <div class="card-body">
        <h6 class="card-title">Storage Scan Report</h6>

        <form action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="fromdate">From Date</label>
                        <input type="date" name="fromdate" id="fromdate" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="todate">To Date</label>
                        <input type="date" name="todate" id="todate" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="from_barcode">From Barcode</label>
                        <input type="text" name="from_barcode" id="from_barcode" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="to_barcode">To Barcode</label>
                        <input type="text" name="to_barcode" id="to_barcode" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="item_id">Part No / Item Name</label>
                        <select name="item_id" id="item_id" class="form-control js-example-basic-single">
                            <option value="">--select--</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->item_code ?? $item->code ?? '' }} - {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="transaction_type">Transaction Type</label>
                        <select name="transaction_type" id="transaction_type" class="form-control js-example-basic-single">
                            <option value="">-- Select --</option>
                            <option value="grn">GRN</option>
                            <option value="production">Production</option>
                        </select>

                    </div>
                </div>

            </div>

            <div style="display: flex; justify-content: center;">
                <div class="col-md-12">
                    <button class="btn btn-primary" name="button" type="submit" value="1" style="margin: 5px;">View</button>
                    <button class="btn btn-success" name="button" type="submit" value="2">Excel</button>
                </div>
            </div>
        </form>
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

@endpush
