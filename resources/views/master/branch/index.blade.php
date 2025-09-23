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
@include('messages')

<div class="card">
    <div class="card-body">
        <h6 class="card-title">Branch Master</h6>

        <div class="row">
            <div class="col-md-12 text-right">





                <a class="btn " href="{{route('branch.create')}}"> Add Branch
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" data-toggle="tooltip" data-placement="bottom" title="Add Branch" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-toggle="tooltip" data-placement="bottom" title="Add Product" class="feather feather-plus-circle text-primary">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16">

                        </line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </a>

                {{-- <a href="{{ route("branch.download.xls", ['format'=>'xls','id'=>null])}}" class="btn " data-toggle="tooltip" data-placement="bottom" title="Export Excel" type="button">Export<i class="mdi mdi-file-excel text-primary" style="font-size: 24px;"></i> </a> --}}


            </div>
        </div>
        {{-- </div> --}}
        <div class="table-responsive">
            <table id="dataTableExample" class="table">
                <thead>
                    <tr>
                        <th align="center" style="width:80px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI.No</th>
                        <th>Branch Name</th>
                        <th>Branch Code</th>
                        <th>Address</th>
                        <th>GST Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $branch = $master;
                    $i = $branch->perPage() * ($branch->currentPage() - 1);
                    ?>
                    @foreach($branch as $branches)
                    <tr>
                        <td align="center" style="width: 20px">

                            {{ ++$i }}
                        </td>

                        <td align="left">
                            {{$branches->name}}
                        </td>
                        <td align="left">
                            {{$branches->branch_code}}
                        </td>

                        <td align="left">
                            {{$branches->address}}
                        </td>

                        <td align="left">
                            {{$branches->gst_no}}
                        </td>

                        </td>

                        <div class="btn-group title-quick-actions">

                            <td width="150px"><a href="{{route('branch.edit',$branches->id)}}" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit text-primary">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg></a>
                                <div class="btn-group">
                                    <form method="POST" action="/branch/{{ $branches->id }}" onsubmit="return confirm('Are you sure, You want to delete this Branch?')">
                                        <!-- CSRF Token -->
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <!-- Method Spoofing for DELETE -->
                                        <input type="hidden" name="_method" value="DELETE">

                                        <button type="submit" class="btn" data-toggle="tooltip" title="Delete">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </div>


                    </tr>
                    @endforeach

                    @if($i==0)
                    <tr>
                        <td align="center" colspan="8" style="color: red">No Record Found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
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
