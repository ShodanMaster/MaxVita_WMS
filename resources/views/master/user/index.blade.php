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
                <div class="card-body">
                    <h6 class="card-title">User Master</h6>
                    @include('messages')
                    <div class="row">
                        <div class="col-md-12 text-right">

                            <a class="btn " href="{{route('user.create')}}"> Add User
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" data-toggle="tooltip" data-placement="bottom" title="Add User" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-toggle="tooltip" data-placement="bottom" title="Add User" class="feather feather-plus-circle text-primary">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16">

                                    </line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                            </a>

                            <!-- Button trigger modal -->
                            <button type="button" class="btn" data-toggle="modal" data-target="#uploadModal">
                                <i data-feather="upload" class="text-primary" style="font-size: 24px;"></i><b> Upload </b>
                            </button>

                            <a href="{{ route("user-excel-export")}}" class="btn " data-toggle="tooltip" data-placement="bottom" title="Export Excel" type="button">Export<i class="mdi mdi-file-excel text-primary" style="font-size: 24px;"></i> </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="usersTable" class="table masterTable">
                            <thead>
                                <tr>
                                    <th align="center" style="width:80px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI.No</th>
                                    <th>Name</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>User Type</th>
                                    <th>Location</th>
                                    <th>Permission Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">User Excel Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user-excel-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excelFile" class="form-label"></label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <span class="mt-2">You can download excel in predefined format by <a href="{{ URL::to( '/excel_templates/master_templates/user_template.xlsx')}}" class="text-primary ">Clicking Here</a></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="uploadButton">Upload</button>
                </div>
            </form>
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
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('get-users') }}",
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'user_type', name: 'user_type' },
            { data: 'location_name', name: 'location_name' },
            { data: 'permission_level', name: 'permission_level' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#uploadModal form').on('submit', function() {
        $('#uploadButton').prop('disabled', true).text('Uploading...');
    });
</script>
@endpush
