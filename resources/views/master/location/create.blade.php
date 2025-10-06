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




<style>
    #prifix{
     text-transform: uppercase;
    }
 </style>

@endpush
@section('content')
@include('messages')
<div class="container">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        @if (isset($location))
                            Update Location
                        @else
                            Add Location
                        @endif
                    </h5>

                    <form
                        action="{{ isset($location) ? route('location.update', $location->id) : route('location.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off"
                    >
                        @csrf
                        @if (isset($location))
                            @method('PATCH')
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        Location Name <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('name', $location->name ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="prefix" class="col-sm-4 control-label">
                                        Prefix <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="prefix"
                                        id="prifix"
                                        maxlength="3"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('prefix', $location->prefix ?? '') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="branch_id" class="col-sm-4 control-label">
                                        Warehouse Name <font color="#FF0000">*</font>
                                    </label>
                                    <select
                                        name="branch_id"
                                        class="js-example-basic-single form-select mandatory"
                                        style="width: 100%;"
                                        required
                                    >
                                        <option value="">--select--</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id', $location->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="location_type" class="col-sm-4 control-label">
                                        Location Type <font color="#FF0000">*</font>
                                    </label>
                                    <select
                                        name="location_type"
                                        class="js-example-basic-single form-select"
                                        required
                                    >
                                        <option value="">--select--</option>
                                        <option value="1" {{ old('location_type', $location->location_type ?? '') == '1' ? 'selected' : '' }}>QC Pending</option>
                                        <option value="2" {{ old('location_type', $location->location_type ?? '') == '2' ? 'selected' : '' }}>Storage</option>
                                        <option value="3" {{ old('location_type', $location->location_type ?? '') == '3' ? 'selected' : '' }}>Rejection</option>
                                        <option value="4" {{ old('location_type', $location->location_type ?? '') == '4' ? 'selected' : '' }}>Dispatch</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="description" class="col-sm-4 control-label">
                                        Description
                                    </label>
                                    <textarea
                                        name="description"
                                        class="form-control form-control-sm"
                                        rows="4"
                                    >{{ old('description', $location->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($location) ? 'Update' : 'Add' }}
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
