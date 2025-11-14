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
            <h6 class="card-title">Opening Stock Detailed Report </h6>

            <div class="table-responsive">
                {{-- <table class="table"> --}}
                <table id="openingStockDetailed" class="table">
                    <thead>
                        <tr>
                            <th align="center" style="width:80px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI.No</th>
                            <th>Opening Number</th>
                            <th>Item Name</th>
                            <th>Manf. Date</th>
                            <th>Expiry Date</th>
                            <th>Bin Name</th>
                            <th>Total Quantity</th>
                            <th>Number of Barcodes</th>
                            <th>Batch</th>
                            <th>Location</th>
                            <th>Branch</th>
                            <th>User</th>
                            <th>Status</th>
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
        $('#openingStockDetailed').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: '{{route("get-opening-stock-detailed") }}',
                type: 'POST',
                data: function(d) {
                    d.fromdate = '{{ $from_date ?? "" }}';
                    d.to_date = '{{ $todate ?? "" }}';
                    d.opennumber = '{{ $opennumber ?? "" }}';
                    d.item_id = '{{ $item_id ?? "" }}';
                    d.opennumberid = "{{$id ?? '' }}";

                }

            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'opening_number',
                    name: 'opening_number'
                },
                {
                    data: 'item_name',
                    name: 'item_name'
                },
                {
                    data: 'manufacture_date',
                    name: 'manufacture_date'
                },
                {
                    data: 'best_before',
                    name: 'best_before'
                },
                {
                    data: 'bin',
                    name: 'bin'
                },
                {
                    data: 'total_quantity',
                    name: 'total_quantity'
                },
                {
                    data: 'number_of_barcodes',
                    name: 'number_of_barcodes'
                },
                {
                    data: 'batch',
                    name: 'batch'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'branch',
                    name: 'branch'
                },
                {
                    data: 'user',
                    name: 'user'
                },
                {
                    data: 'status',
                    name: 'status'
                }
            ],
            order: [
                [1, 'asc']
            ],
            autoWidth: false
        });
    });
</script>
@endpush