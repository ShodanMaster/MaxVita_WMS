@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/@mdi/css/materialdesignicons.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush


@section('content')
@include('sweetalert::alert')
<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Storage Scan</h3>
                </div>
                <div class="card-body">
                    @csrf
                    <div class="form-group row">
                        <label for="grn_no" class="col-md-4 control-label">GRN No</label>
                        <div class="col-sm-8">
                            <input type="text" id="grn_no" name="grn_no" class="form-control form-control-sm" required readonly value="{{ $grnNumber }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="bin" class="col-sm-4 control-label">
                            Bin <font color="#FF0000">*</font>
                        </label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" id="bin" name="bin" class="form-control form-control-sm" required oninput="binExists()" value="{{ isset($bin) ? $bin : '' }}">
                                <button type="button" class="btn btn-sm" id="reset-button" title="Reset Bin" style="display: none" onclick="resetButton()">
					                <i class="link-icon" data-feather="refresh-ccw"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="barcode" class="col-sm-4 control-label">
                            Barcode <font color="#FF0000">*</font>
                        </label>
                        <div class="col-sm-8">
                            <input type="hidden" id="scanned_barcodes" name="scanned_barcodes">
                            <input type="text" id="barcode" name="barcode" class="form-control form-control-sm" oninput="storageScan()" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#detailsModal">
                        View Details
                    </button>
                </div>
            </div>
            <div class="card">
                <div class="card-header mt-2" style="overflow-x: auto;overflow-y:auto;">
                    <table class="table">

                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Message</th>
                            </tr>

                        </thead>
                        <tbody id="dataTable">
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Storage Scan Item Display</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>B.Qty</th>
                            <th>S.Qty</th>
                            <th>R.Qty</th>
                        </tr>
                    </thead>
                    <tbody id="balancegrid">
                        @foreach($grn->grnSubs as $grnSub)
                            <tr>
                                <td>{{$grnSub->item->name}}</td>
                                <td>{{$grnSub->total_quantity - $grnSub->scanned_quantity}}</td>
                                <td>{{$grnSub->scanned_quantity}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    function binExists() {
        const bin = document.getElementById('bin');
        const binValue = bin.value.trim();
        const resetButton = document.getElementById('reset-button');
        const barcode = document.getElementById('barcode');

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.bin-exists') }}",
            data: {
                bin : binValue
            },
            dataType: "json",
            success: function (response) {
                console.log(response);

                if (response.status === 200) {
                    bin.readOnly = true;
                    resetButton.style.display = 'block';
                    barcode.focus();
                } else {
                    alert('Bin Not Found!');
                    bin.value = '';
                    bin.readOnly = false;
                    bin.focus();
                    resetButton.style.display = 'none';
                }
            }
        });
    }

    function resetButton(){
        const bin = document.getElementById('bin');
        const resetButton = document.getElementById('reset-button');

        bin.value = '';
        bin.readOnly = false;
        bin.focus();
        resetButton.style.display = 'none';
    }

    function storageScan(){
        const bin = document.getElementById('bin');
        const barcode = document.getElementById('barcode');

        console.log(barcode.value);

        if(bin.value ==''){
            alert('Enter Bin!');
            bin.focus();
            return false;
        }

        if(barcode.value ==''){
            alert('Enter Barcode!');
            barcode.focus();
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.storagescan') }}",
            data: {
                bin: bin.value,
                barcode : barcode.value
            },
            dataType: "json",
            success: function (response) {
                console.log(response);

            }
        });
    }
</script>

@endpush
