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
<div class="content-header">
    @include('messages')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        @if (isset($bin))
                            Update Bin
                        @else
                            Add Bin
                        @endif
                    </h5>

                    <form
                        action="{{ isset($bin) ? route('bin.update', $bin->id) : route('bin.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off"
                    >
                        @csrf
                        @if (isset($bin))
                            @method('PATCH')
                        @endif

                        <!-- Bin Form Fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="location_id" class="col-sm-4 control-label">
                                        Storage Location <font color="#FF0000">*</font>
                                    </label>
                                    <select
                                        name="location_id"
                                        class="js-example-basic-single form-select mandatory"
                                        style="width: 100%;"
                                        required
                                    >
                                        <option value="">--select--</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ old('location_id', $bin->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        Bin Name <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('name', $bin->name ?? '') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Bin Name & Address -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="bin_type" class="col-sm-4 control-label">
                                        Bin Type <font color="#FF0000">*</font>
                                    </label>
                                    <select
                                        name="bin_type"
                                        class="js-example-basic-single form-select mandatory"
                                        style="width: 100%;"
                                        required
                                    >
                                        <option value="">--select--</option>
                                        <option value="FG" {{ old('bin_type', $bin->bin_type ?? '') == 'FG' ? 'selected' : '' }}>FG</option>
                                        <option value="RM" {{ old('bin_type', $bin->bin_type ?? '') == 'RM' ? 'selected' : '' }}>RM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="description" class="col-sm-4 control-label">
                                        Description
                                    </label>
                                    <textarea
                                        name="description"
                                        class="form-control form-control-sm"
                                        rows="4"
                                    >{{ old('description', $bin->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($bin) ? 'Update' : 'Add' }}
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
