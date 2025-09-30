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
                        @if (isset($item))
                            Update Item
                        @else
                            Add Item
                        @endif
                    </h5>

                    <form
                        action="{{ isset($item) ? route('item.update', $item->id) : route('item.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off"
                    >
                        @csrf
                        @if (isset($item))
                            @method('PATCH')
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="col-sm-4 control-label">
                                    Item Group <font color="#FF0000">*</font>
                                </label>
                                <select
                                    id="category"
                                    name="category_id"
                                    class="js-example-basic-single form-select mandatory"
                                    style="width: 100%;"
                                    required
                                >
                                    <option value="">--select--</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $item->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="uom" class="col-sm-4 control-label">
                                    Uom <font color="#FF0000">*</font>
                                </label>
                                <select
                                    id="uom"
                                    name="uom_id"
                                    class="js-example-basic-single form-select mandatory"
                                    style="width: 100%;"
                                    required
                                >
                                    <option value="">--select--</option>
                                    @foreach($uoms as $uom)
                                        <option value="{{ $uom->id }}"
                                            {{ old('uom_id', $item->uom_id ?? '') == $uom->id ? 'selected' : '' }}>
                                            {{ $uom->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        Item Description <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('name', $item->name ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="item_code" class="col-sm-4 control-label">
                                        Item Code <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="item_code"
                                        id="item_code"
                                        class="form-control form-control-sm"
                                        required
                                        value="{{ old('item_code', $item->item_code ?? '') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="in_stock" class="col-sm-4 control-label">
                                        In Stock
                                    </label>
                                    <input
                                        type="number"
                                        name="in_stock"
                                        id="in_stock"
                                        min="1"
                                        class="form-control form-control-sm"
                                        value="{{ old('in_stock', $item->in_stock ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="item_type" class="col-sm-4 control-label">
                                        Item Type <font color="#FF0000">*</font>
                                    </label>
                                    <select
                                        name="item_type"
                                        class="js-example-basic-single form-select"
                                        required
                                        onchange="toggleFgFields(this)"
                                    >
                                        <option value="">--select--</option>
                                        <option value="FG" {{ old('item_type', $item->item_type ?? '') == 'FG' ? 'selected' : '' }}>FG</option>
                                        <option value="RM" {{ old('item_type', $item->item_type ?? '') == 'RM' ? 'selected' : '' }}>RM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="fgFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="sku_code" class="col-sm-4 control-label">
                                            SKU Code <font color="#FF0000">*</font>
                                        </label>
                                        <input
                                            type="text"
                                            name="sku_code"
                                            id="sku_code"
                                            class="form-control form-control-sm"
                                            value="{{ old('sku_code', $item->sku_code ?? '') }}"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="spq_quantity" class="col-sm-4 control-label">
                                            SPQ Quantity <font color="#FF0000">*</font>
                                        </label>
                                        <input
                                            type="integer"
                                            min="1"
                                            name="spq_quantity"
                                            class="form-control form-control-sm"
                                            value="{{ old('spq_quantity', $item->spq_quantity ?? '') }}"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="gst_rate" class="col-sm-4 control-label">
                                            GST Rate
                                        </label>
                                        <input
                                            type="text"
                                            id="gst_rate"
                                            name="gst_rate"
                                            class="form-control form-control-sm"
                                            value="{{ old('gst_rate', $item->gst_rate ?? '') }}"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($item) ? 'Update' : 'Add' }}
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

<script>

    function toggleFgFields(select) {
        var fgFields = document.getElementById('fgFields');
        var requiredFields = fgFields.querySelectorAll('input');

        if (select.value == 'FG') {
            fgFields.style.display = 'block';
            requiredFields.forEach(function(field) {

                if (field.name !== 'gst_rate') {
                    field.setAttribute('required', true);
                }
            });
        } else {
            fgFields.style.display = 'none';
            requiredFields.forEach(function(field) {
                field.removeAttribute('required');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var itemType = document.querySelector('select[name="item_type"]').value;
        var fgFields = document.getElementById('fgFields');
        var requiredFields = fgFields.querySelectorAll('input');

        if (itemType == 'FG') {
            fgFields.style.display = 'block';
            requiredFields.forEach(function(field) {

                if (field.name !== 'gst_rate') {
                    field.setAttribute('required', true);
                }
            });
        } else {
            fgFields.style.display = 'none';
        }
    });

</script>
@endpush
