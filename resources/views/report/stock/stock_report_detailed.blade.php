@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush
{{-- ✅ Add this here --}}
<style>
    #stockDetailed th,
    #stockDetailed td {
        white-space: nowrap;

        padding: 8px 15px;

        vertical-align: middle;
        text-align: left;
    }


    #stockDetailed th:nth-child(7),
    #stockDetailed td:nth-child(7) {
        min-width: 200px;

    }

    #stockDetailed th:nth-child(14),
    #stockDetailed td:nth-child(14) {
        min-width: 150px;

    }

    #stockDetailed th:nth-child(13),
    #stockDetailed td:nth-child(13) {
        min-width: 120px;

        text-align: center;
    }


    .dataTables_wrapper {
        overflow-x: auto;
    }
</style>
@section('content')
@include('sweetalert::alert')
@include('messages')

<div class="content-header">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Stock Detailed Report</h6>

            {{-- Color code legend --}}
            <div class="table-responsive mb-3">
                <table border="0">
                    <tr>
                        <td style="background-color:#ff9999" width="30"></td>
                        <td><small>Expired</small></td>
                        <td style="background-color:#ffbf80" width="30"></td>
                        <td><small>Less than 1 month</small></td>
                        <td style="background-color:#ffff80" width="30"></td>
                        <td><small>Less than 6 months</small></td>
                        <td style="background-color:#80ff80" width="30"></td>
                        <td><small>More than 6 months</small></td>
                    </tr>
                </table>
            </div>

            {{-- Main DataTable --}}
            <div class="table-responsive">
                <table id="stockDetailed" class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>SI.No</th>
                            <th>Location</th>
                            <th>Bin</th>
                            <th>Serial Number</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Item</th>
                            <th>SPQ Qty</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th>Manufacture Date</th>
                            <th>Expiry Date</th>
                            <th>No. of Days for Expiry</th>
                            <th>Category</th>
                            <th>Stock In Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
    $(document).ready(function() {
        $('#stockDetailed').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: '{{ route("get-stock-detailed") }}',
                type: 'POST',
                data: {
                    fromdate: '{{$from_date  ?? ""}}',
                    todate: '{{$to_date  ?? ""}}',
                    from_barcode: '{{$from_barcode  ?? ""}}',
                    to_barcode: '{{$to_barcode  ?? ""}}',
                    category_id: '{{$category_id  ?? ""}}',
                    item_id: '{{$item_id}}',
                    branch_id: '{{$branch_id}}',
                    location_id: '{{$location_id}}',
                    grn_type: '{{$grn_type  ?? ""}}',
                    bin_id: '{{$bin_id}}',
                    stock_type: '{{$stock_type  ?? ""}}',
                    transaction_id: '{{$transaction_id  ?? ""}}',
                    batch_number: '{{$batch_number  ?? ""}}',
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'location_name',
                    name: 'location_name'
                },
                {
                    data: 'bin_name',
                    name: 'bin_name'
                },
                {
                    data: 'serial',
                    name: 'serial'
                },
                {
                    data: 'net_weight',
                    name: 'net_weight'
                },
                {
                    data: 'uom_name',
                    name: 'uom_name'
                },
                {
                    data: 'item_name',
                    name: 'item_name'
                },
                {
                    data: 'spq_quantity',
                    name: 'spq_quantity'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'date_of_manufacture',
                    name: 'date_of_manufacture'
                },
                {
                    data: 'best_before_date',
                    name: 'best_before_date'
                },
                {
                    data: 'days_before_expiry',
                    name: 'days_before_expiry'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'scan_time',
                    name: 'scan_time'
                },
            ],
            order: [
                [1, 'asc']
            ],
            language: {
                emptyTable: "<div class='text-center fw-bold text-dark py-3'>No data found</div>",
                processing: "<div class='text-center text-primary fw-bold py-3'>Loading...</div>"
            },
            createdRow: function(row, data, dataIndex) {
                const days = parseInt(data.days_before_expiry);
                let color = '';
                if (data.qc_approval_status == '0') color = '#e6e6e6';
                else if (days <= 0) color = '#ff9999';
                else if (days <= 30) color = '#ffbf80';
                else if (days <= 180) color = '#ffff80';
                else color = '#80ff80';
                $(row).css('background-color', color);
            }
        });
    });
</script>
@endpush