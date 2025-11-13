@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
@include('sweetalert::alert')
@include('messages')

<div class="content-header">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Dispatch Scanned Barcodes Report </h6>

            <div class="table-responsive">
                {{-- <table class="table"> --}}
                <table id="DispatchBarcodeDetails" class="table">
                    <thead>
                        <tr>
                            <th align="center" style="width:80px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI.No</th>
                            <th>Dispatch Number</th>
                            <th>Dispatch Date</th>
                            <th>Item</th>
                            <th>Uom</th>
                            <th>Barcode</th>
                            <th>Dispatched Quantity</th>
                            <th>Scanned By</th>
                            <th>Scanned Time</th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>




        </div>


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
    $(document).ready(function() {
        $('#DispatchBarcodeDetails').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: '{{ route("get-DispatchBarcodeDetails") }}',
                type: 'POST',
                data: function(d) {
                    d.fromdate = "{{$from_date ?? ''}}";
                    d.todate = "{{$to_date ?? '' }}";
                    d.formbarcode = '{{ $frombarcode ?? "" }}';
                    d.tobarcode = '{{ $tobarcode ?? "" }}';
                    d.dispatch_number = '{{ $dispatch_number ?? "" }}';
                    d.dispatch_type = '{{ $dispatch_type ?? "" }}';
                    d.dispatchentryid = '{{ $id ?? "" }}';
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'dispatch_number',
                    name: 'dispatch_number'
                },
                {
                    data: 'dispatch_date',
                    name: 'dispatch_date'
                },
                {
                    data: 'item_name',
                    name: 'item_name'
                },
                {
                    data: 'uom',
                    name: 'uom'
                },
                {
                    data: 'barcode',
                    name: 'barcode'
                },
                {
                    data: 'dispatched_quantity',
                    name: 'dispatched_quantity'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'scan_time',
                    name: 'scan_time'
                },
            ],
            order: [
                [1, 'asc']
            ],
            autoWidth: false
        });
    });
</script>
@endpush