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
            <h6 class="card-title">Grn PO Wise Report </h6>

            <div class="table-responsive">
                {{-- <table class="table"> --}}
                <table id="grnPo" class="table">
                    <thead>
                        <tr>
                            <th align="center" style="width:80px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI.No</th>
                            <th>GRN No</th>
                            <th>PO Number</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Batch No</th>
                            <th>Spq</th>
                            <th>Manf. Date</th>
                            <th>Expiry Date</th>
                            <th>No of Barcodes</th>
                            <th>Action</th>

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
        $('#grnPo').DataTable({
            processing: true,
            serverSide: true,
            paging: true, // <-- Disable pagination
            ajax: {
                url: '{{ route("get-grn-po") }}',
                type: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.formdate = "{{ $from_date ?? '' }}";
                    d.todate = "{{$to_date ?? '' }}";
                    d.grn_number = "{{$grn_number?? ''}}";
                    d.grn_type = "{{$grn_type ?? ' ' }}";
                    d.item_id = "{{$item_id ?? '' }}";
                    d.item = "{{$item ?? '' }}";

                }
            },
            columns: [{
                    data: 'DT_RowIndex', // Index column for DataTable
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'grn_number', // GRN Number column
                    name: 'grn_number'
                },
                {
                    data: 'purchase_number', // Purchase Number column
                    name: 'purchase_number'
                },
                {
                    data: 'item_name', // Item Name column
                    name: 'item_name'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },

                {
                    data: 'uom_name', // UOM (Unit of Measurement) column
                    name: 'uom_name'
                },
                {
                    data: 'batch_number', // Batch Number column
                    name: 'batch_number'
                },

                {
                    data: 'spq_quantity', // Batch Number column
                    name: 'spq_quantity'
                },

                {
                    data: 'date_of_manufacture', // Manufacture Date column
                    name: 'date_of_manufacture'
                },
                {
                    data: 'best_before_date', // Expiry Date column
                    name: 'best_before_date'
                },
                {
                    data: 'number_of_barcodes', // Number of Barcodes column
                    name: 'number_of_barcodes'
                },
                {
                    data: 'action', // Action column (buttons/links)
                    name: 'action',
                    orderable: false,
                    searchable: false
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
