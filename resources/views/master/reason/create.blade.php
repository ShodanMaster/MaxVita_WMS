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

<style>
    #bin_code {
        text-transform: uppercase;
    }
</style>

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        @if (isset($reason))
                            Update Reason
                        @else
                            Add Reason
                        @endif
                    </h5>

                    <form
                        action="{{ isset($reason) ? route('reason.update', $reason->id) : route('reason.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off"
                    >
                        @csrf
                        @if (isset($reason))
                            @method('PATCH')
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        Reason <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="reason"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('reason', $reason->reason ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="description" class="col-sm-4 control-label">
                                        Description <font color="#FF0000">*</font>
                                    </label>
                                    <textarea
                                        name="description"
                                        required
                                        class="form-control form-control-sm"
                                        rows="4"
                                    >{{ old('description', $reason->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($reason) ? 'Update' : 'Add' }}
                            </button>
                            <input type="reset" class="btn btn-default" value="Clear" />
                        </div>
                    </form>
                </div>
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
@endpush
