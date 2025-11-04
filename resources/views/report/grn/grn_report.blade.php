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
        <h6 class="card-title">Grn Report</h6>
        <form action="{{route('grn-report.store')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="fromdate">From Date</label>
                        <input type="date" name="from_date" id="fromdate" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="todate">To Date</label>
                        <input type="date" name="to_date" id="todate" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="grn_number">Grn Number</label>
                        <input type="text" name="grn_number" id="grn_number" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="grn_type">GRN Type</label>
                        <select name="grn_type" id="grn_type" class="js-example-basic-single form-control" onchange="filterItem()">
                            <option value="">----Select----</option>
                            <option value="RM">RM</option>
                            <option value="FG">FG</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <div class="col-md-12 form-group">
                        <label for="item_id">Part No / Item Name</label>
                        <select name="item_id" id="item_id" class="js-example-basic-single form-control">
                            <option value="">--select--</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} ({{ $item->item_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: center;">
                <div class="col-md-12">
                    <button type="submit" name="button" value="1" class="btn btn-primary" style="margin: 5px;">Summary</button>
                    <button type="submit" name="button" value="2" class="btn btn-success">Summary Excel</button>
                    <button type="submit" name="button" value="7" class="btn btn-primary" style="margin: 5px;">PO Wise</button>
                    <button type="submit" name="button" value="8" class="btn btn-success">PO Wise Excel</button>
                    <button type="submit" name="button" value="3" class="btn btn-primary" style="margin: 5px;">Item Wise</button>
                    <button type="submit" name="button" value="4" class="btn btn-success">Item Wise Excel</button>
                    <button type="submit" name="button" value="5" class="btn btn-primary" style="margin: 5px;">Detailed</button>
                    <button type="submit" name="button" value="6" class="btn btn-success">Detailed Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>




<script>
    function filterItem() {

        var grnType = $('#grn_type').val();
        var categoryId = $('#category_id').val();
        var purchaseNumber = $('#purchase_number').val();

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.getgrnitems') }}",
            data: {
                grn_type: grnType,
            },
            dataType: "json",
            success: function(response) {
                $('#item_id').empty();
                $('#item_id').append('<option value="">--select--</option>');
                if (response && response.length > 0) {
                    response.forEach(function(item) {

                        $('#item_id').append(
                            `<option value="${item.id}">${item.item_code}/${item.name}</option>`
                        );
                    });

                }
            }
        });
    }
</script>


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
