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
        <h6 class="card-title">Stock Out Report</h6>

        <form action="{{ route('stock-out-report.store') }}" method="POST" enctype="multipart/form-data">
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
                        <label for="reason">Reason</label>
                        <select name="reason" id="reason" class="js-example-basic-single form-control">
                            <option value="">--select--</option>
                            @foreach($reasons as $reason)
                            <option value="{{ $reason->reason }}">{{ $reason->reason }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: center;">
                <div class="col-md-12">
                    <button type="submit" name="button" value="1" class="btn btn-primary" style="margin: 5px;">Summary</button>
                    <button type="submit" name="button" value="2" class="btn btn-success">Summary Excel</button>


                    <button type="submit" name="button" value="3" class="btn btn-primary" style="margin: 5px;">Detailed</button>
                    <button type="submit" name="button" value="4" class="btn btn-success">Detailed Excel</button>
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