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
        <h6 class="card-title">Stock Report</h6>
        <form action="{{route('stock-report.store')}}" method="POST" enctype="multipart/form-data">
            @csrf


            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="fromdate">From Date</label>
                    <input type="date" name="from_date" id="fromdate" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="todate">To Date</label>
                    <input type="date" name="to_date" id="todate" class="form-control">
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="frombarcode">From Barcode</label>
                    <input type="text" name="frombarcode" id="frombarcode" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="tobarcode">To Barcode</label>
                    <input type="text" name="tobarcode" id="tobarcode" class="form-control">
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control js-example-basic-single">
                        <option value="">--select--</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">

                    <label for="item_id">Item Name</label>
                    <select name="item_id" id="item_id" class="form-control js-example-basic-single">
                        <option value="">--select--</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} ({{ $item->item_code }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="branch_id">Storage Branch</label>
                    <select name="branch_id" id="branch_id" class="form-control js-example-basic-single" onchange="filterlocation()">
                        <option value="">--select--</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="location_id">Storage Location</label>
                    <select name="location_id" id="location_id" class="form-control js-example-basic-single">
                        <option value="">--select--</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="grn_type">Item Type</label>
                    <select name="grn_type" id="grn_type" class="form-control js-example-basic-single">
                        <option value="">----Select----</option>
                        <option value="RM">RM</option>
                        <option value="FG">FG</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="bin_id">Bin</label>
                    <select name="bin_id" id="bin_id" class="form-control js-example-basic-single">
                        <option value="">--select--</option>
                        @foreach($bins as $bin)
                        <option value="{{ $bin->id }}">{{ $bin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="stock_type">Stock Type</label>
                    <select name="stock_type" id="stock_type" class="form-control js-example-basic-single">
                        <option value="1">IN STOCK</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="batch_number">Batch</label>
                    <select name="batch_number" id="batch_number" class="form-control js-example-basic-single">
                        <option value="">--select--</option>
                        @foreach( $barcodes as $barcode)
                        <option value="{{ $barcode->batch_number }}">{{ $barcode->batch_number }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="d-flex justify-content-start mt-3">
                <div class="col-md-12 text-start">
                    <button type="submit" name="button" value="1" class="btn btn-primary m-1">Summary</button>
                    <button type="submit" name="button" value="2" class="btn btn-success m-1">Summary Excel</button>
                    <button type="submit" name="button" value="3" class="btn btn-primary m-1">Bin Wise</button>
                    <button type="submit" name="button" value="4" class="btn btn-success m-1">Bin Wise Excel</button>
                    <button type="submit" name="button" value="5" class="btn btn-primary m-1">Detailed</button>
                    <button type="submit" name="button" value="6" class="btn btn-success m-1">Detailed Excel</button>
                    <button type="submit" name="button" value="7" class="btn btn-primary m-1">Expirewise</button>
                    <button type="submit" name="button" value="8" class="btn btn-success m-1">Expirewise Excel</button>
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

<script>

    function filterlocation() {
        var branch = $('#branch_id').val();

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getlocations') }}",
            data: {
                branch_id: branch,
            },
            dataType: "json",
            success: function(response) {
                $('#location_id').empty();
                $('#location_id').append('<option value="">--select--</option>');

                if (response && response.length > 0) {
                    response.forEach(function(location) {
                        $('#location_id').append(
                            `<option value="${location.id}">${location.location_code} / ${location.name}</option>`
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching locations:', error);
            }
        });
    }
</script>
@endpush
