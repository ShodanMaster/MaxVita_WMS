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
                    <h3 class="card-title">Barcode Reprint</h3>
                </div>
                <form action="{{ route('barcode-reprint.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="transaction_type" class="form-label">Transaction Type</label>
                                <select name="transaction_type" id="transaction_type" class="js-example-basic-single form-select2 mandatory" onchange="getDocumentNumbers()" required>
                                    <option value="" disabled selected>-- Select Transaction Type --</option>
                                    <option value="grn">GRN</option>
                                    <option value="production">Production</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="document_number" class="form-label">Document Number</label>
                                <select name="document_number" id="document_number" class="js-example-basic-single form-select2 mandatory" required>
                                    <option value="" disabled selected>-- Select Document Number --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="barcode_from" class="form-label">Barcode From</label>
                                <input type="text" class="form-control" name="barcode_from" id="barcode_from">
                            </div>
                            <div class="col-md-6">
                                <label for="barcode_to" class="form-label">Barcode to</label>
                                <input type="text" class="form-control" name="barcode_to" id="barcode_to">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Print
                        </button>
                    </div>
                </form>
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
    function getDocumentNumbers(){
        let transactionType = $('#transaction_type').val();

        console.log(transactionType);

        $.ajax({
            type: "POST",
            url: "{{ route('ajax.get-reprint-numbers') }}",
            data: {
                transaction_type : transactionType,
            },
            dataType: "json",
            success: function (response) {
                let $select = $('#document_number');
                if(response && response.length > 0){
                    $select.empty();
                    $select.append('<option value="">-- Select Document Number --</option>');

                    $.each(response, function (index, document) {
                        $select.append(`<option value="${document.id}">${document.document_number}</option>`);
                    });

                }else{
                    $select.empty();
                    $select.append('<option value="">No Document Numbers found</option>');
                    $select.trigger('change');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                let $select = $('#document_number');
                $select.empty();
                $select.append('<option value="">No Document Numbers found</option>');
            }
        });
    }
</script>

@endpush
