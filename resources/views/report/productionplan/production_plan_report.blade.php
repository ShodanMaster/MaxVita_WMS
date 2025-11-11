@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
@include('sweetalert::alert')
@include('messages')


<div class="card mt-4">
    <div class="card-body">
        <h6 class="card-title">Production Plan Report</h6>

        <form action="{{route('production-plan-report.store')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="fromdate">From Date</label>
                        <input type="date" id="fromdate" name="fromdate" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="todate">To Date</label>
                        <input type="date" id="todate" name="todate" class="form-control">
                    </div>
                </div>
            </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <div class="col-md-12 form-group">
                            <label for="frombarcode">From Barcode</label>
                            <input type="text" name="frombarcode" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6 form-group">
                        <div class="col-md-12 form-group">
                            <label for="tobarcode">To Barcode</label>
                            <input type="text" name="tobarcode" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <div class="col-md-12 form-group">
                            <label for="plan_no">Plan Number</label>
                            <select name="plan_no" id="plan_no" class="js-example-basic-single form-control">
                                <option value="">--select--</option>
                                @foreach($production_plans as $production_plan)
                                <option value="{{ $production_plan->id }}">
                                    {{ $production_plan->plan_number }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 form-group">
                        <div class="col-md-12 form-group">
                            <label for="status">Status</label>
                            <select name="status" class="js-example-basic-single form-control">
                                <option value="">----Select----</option>
                                <option value="0">Open</option>
                                <option value="1">barcode generation pending</option>
                                <option value="2">fg storage scan pending</option>
                                <option value="3">closed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-3">
                    <div class="col-md-12 text-start">
                        <button type="submit" name="button" value="1" class="btn btn-primary" style="margin: 5px;">Summary</button>
                        <button type="submit" name="button" value="2" class="btn btn-success">Summary Excel</button>
                        <button type="submit" name="button" value="3" class="btn btn-primary" style="margin: 5px;">Detailed</button>
                        <button type="submit" name="button" value="4" class="btn btn-success">Detailed Excel</button>
                        <button type="submit" name="button" value="5" class="btn btn-primary">Production Scanned Barcodes</button>
                        <button type="submit" name="button" value="6" class="btn btn-success"> Production Barcode Excel</button>
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
