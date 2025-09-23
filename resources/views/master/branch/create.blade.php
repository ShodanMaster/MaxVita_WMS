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
    #branch_code {
        text-transform: uppercase;
    }
</style>

@section('content')
<div class="content-header">
    @include('messages')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if (isset($branch))
                            Update Branch
                        @else
                            Add Branch
                        @endif
                    </h3>
                </div>

                <div class="col-md-6">
                    <form
                        action="{{ isset($branch) ? route('branch.update', $branch->id) : route('branch.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off"
                    >
                        @csrf
                        @if (isset($branch))
                            @method('PATCH')
                        @endif

                        <div class="card-body">
                            <div class="form-group row">
                                <label for="branch_code" class="col-sm-4 control-label">
                                    Branch Code <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <input
                                        type="text"
                                        name="branch_code"
                                        id="branch_code"
                                        maxlength="2"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('branch_code', $branch->branch_code ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-sm-4 control-label">
                                    Branch Name <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('name', $branch->name ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="address" class="col-sm-4 control-label">
                                    Address <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <textarea
                                        name="address"
                                        required
                                        class="form-control form-control-sm"
                                        rows="4"
                                    >{{ old('address', $branch->address ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="gst_no" class="col-sm-4 control-label">
                                    GST Number <font color="#FF0000">*</font>
                                </label>
                                <div class="col-sm-8">
                                    <input
                                        type="text"
                                        name="gst_no"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('gst_no', $branch->gst_no ?? '') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($branch) ? 'Update' : 'Add' }}
                            </button>
                            <input type="reset" class="btn btn-default" value="Clear" />
                        </div>
                    </form>
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
@endpush
