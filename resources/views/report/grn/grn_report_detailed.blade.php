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
            <h6 class="card-title">Grn Detailed Report </h6>

            <div class="table-responsive">
                {{-- <table class="table"> --}}
                <table id="grndetailed" class="table">
                    <thead>
                        <tr>
                            <th align="center" style="width:80px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI.No</th>
                            <th>GRN No</th>
                            <th>Item Name</th>
                            <th>Serial No</th>
                            <th>Net weight</th>
                            <th>Batch No</th>
                            <th>Price</th>
                            <th>Total Price</th>

                            <th>Manf. Date</th>
                            <th>Expiry Date</th>
                            <th>GRN Time</th>

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
        $('#grndetailed').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: '{{ route("get-grn-detailed") }}',
                type: 'POST',
                data: function(d) {
                    d.fromdate = "{{ $from_date ?? '' }}";
                    d.todate = "{{ $to_date ?? '' }}";
                    d.grn_number = "{{ $grn_number ?? '' }}";
                    d.grn_numberid = "{{ $id ?? '' }}";
                    d.grn_type = "{{$grn_type ?? ' ' }}";
                    d.item_id = "{{$item_id ?? '' }}";
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'grn_number',
                    name: 'grn_number'
                },

                {
                    data: 'item_name',
                    name: 'item_name'
                },
                {
                    data: 'serial_number',
                    name: 'serial_number'
                },

                {
                    data: 'net_weight', // UOM (Unit of Measurement) column
                    name: 'net_weight'
                },
                {
                    data: 'batch_number', // Batch Number column
                    name: 'batch_number'
                },

                {
                    data: 'price', // Batch Number column
                    name: 'price'
                },

                {
                    data: 'total_price', // Manufacture Date column
                    name: 'total_price'
                },
                {
                    data: 'date_of_manufacture', // Expiry Date column
                    name: 'date_of_manufacture'
                },
                {
                    data: 'best_before_date', // Number of Barcodes column
                    name: 'best_before_date'
                },
                {
                    data: 'created_at', // Number of Barcodes column
                    name: 'created_at'
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
