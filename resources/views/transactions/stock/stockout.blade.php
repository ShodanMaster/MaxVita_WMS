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
                <div class="card-header mt-2">
                    <h3 class="card-title">Stock Out Scan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="reason" class="col-sm-4 control-label">
                            Reason<font color="#FF0000" size="">*</font>
                        </label>
                        <div class="col-sm-8">
                            <select name="reason" id="reason" class="js-example-basic-single form-select2 mandatory">
                                <option value="" disabled selected>-- Select Reson --</option>
                                @forelse ($reasons as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                @empty
                                    <option value="" disabled>-- No Reson Found --</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-4 control-label">
                            Barcode<font color="#FF0000" size="">*</font>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="barcode" name="barcode" class="form-control form-control-sm" oninput="stockOutScan()" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="display: none" id="barcode-card">
                <div class="card-body mt-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
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
    function stockOutScan(){
        const reason = document.getElementById('reason');
        const barcode = document.getElementById('barcode');

        if(reason.value ==''){
            sweetAlertMessage('warning', 'Enter Reason', 'Please enter reason!');
            reason.focus();
            return false;
        }

        if(barcode.value ==''){
            sweetAlertMessage('warning', 'Enter Barcode', 'Please enter barcode!');
            barcode.focus();
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('stock-out.store') }}",
            data: {
                reason_id : reason.value,
                barcode : barcode.value,
            },
            dataType: "json",
            success: function (response) {
                document.getElementById('barcode-card').style.display = 'block';
                if(response){
                    var row = $('<tr>');

                    row.append('<td>' + barcode.value + '</td>');

                    var messageCell = $('<td>').text(response.message);

                    if (response.status === 200) {
                        sweetAlertMessage('success', 'Scanned Successfully', response.message);
                        messageCell.css('color', 'green');
                    } else {
                        sweetAlertMessage('error', 'Scan Unsuccessful', response.message);
                        messageCell.css('color', 'red');
                    }

                    row.append(messageCell);
                    $('#table-body').prepend(row);
                }

                barcode.value = '';

            },
            error: function (xhr) {
                console.error(xhr.responseText);
                sweetAlertMessage('error', 'Error', 'Something went wrong!');
            }
        });
    }
</script>
@endpush
