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
                    <h6 class="card-title">User Permission</h6>
                    <form action="{{ route('permission.store') }}" method="POST" class="form-horizontal validate" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card-body">
                                    <div class="row">
                                        <label for="user_id" class="col-sm-4 control-label">
                                            User Name <font color="#FF0000">*</font>
                                        </label>
                                        <div class="col-sm-8">
                                            <select name="user_id" id="user_id" class="form-control select2">
                                                <option value="">--select--</option>
                                                @forelse ($users as $user)
                                                    <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No Users Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Placeholder for permission checkboxes or other content -->
                        <div class="row m-auto" id="permissioncheck"></div>

                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="reset" class="btn btn-default" onclick="resetall()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    $(function () {

        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        // CSRF token for AJAX
        const csrfToken = $('meta[name="_token"]').attr('content');

        // Load permission structure
        function loadPermissionStructure(callback) {
            $.ajax({
                type: 'POST',
                url: "{{ route('get-permission-menu') }}",
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (response) {
                    let userPermissionHtml = '';
                    for (const x in response) {
                        const menus = response[x].menu;
                        const submenus = response[x].submenu;

                        for (const menu of menus) {
                            userPermissionHtml += `
                                <div class="col-sm-4 mt-4">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h3 class="card-title">${menu.title}</h3>
                                            <div class="text-right">
                                                Select All&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="cbox" style="width: 14px; height: 14px;"
                                                       onclick="toggleMenuPermissions('${menu.link}', this.checked)"
                                                       id="${menu.link}">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <tbody>`;

                            for (const submenu of submenus) {
                                if (submenu.menu_id === menu.id) {
                                    userPermissionHtml += `
                                        <tr>
                                            <td style="width: 90%">${submenu.title}</td>
                                            <td>
                                                <input class="checkboxstyle ${menu.link}"
                                                       type="checkbox"
                                                       name="${submenu.id}"
                                                       id="${submenu.id}${menu.link}"
                                                       value="1">
                                            </td>
                                        </tr>`;
                                }
                            }

                            userPermissionHtml += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>`;
                        }
                    }
                    $('#permissioncheck').html(userPermissionHtml);
                    if (typeof callback === 'function') callback();
                }
            });
        }

        // Apply existing permissions for selected user
        function applyUserPermissions(userId) {
            if (!userId) return;

            $.ajax({
                type: 'POST',
                url: "{{ route('get-permission-menu') }}",
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { uid: userId },
                success: function (response) {
                    const { menu, submenu, permission } = response[0] || {};
                    const permissions = permission || [];

                    menu?.forEach(menuItem => {
                        let allChecked = true;

                        submenu?.forEach(submenuItem => {
                            if (submenuItem.menu_id === menuItem.id) {
                                const inputId = `#${submenuItem.id}${menuItem.link}`;
                                const isChecked = permissions.some(p => p.submenu_id === submenuItem.id);
                                $(inputId).prop('checked', isChecked);

                                if (!isChecked) {
                                    allChecked = false;
                                }
                            }
                        });

                        const groupCheckbox = `#${menuItem.link}[name="cbox"]`;
                        $(groupCheckbox).prop('checked', allChecked);
                    });
                }
            });
        }

        // On user select change
        $('#user_id').change(function () {
            const userId = $(this).val();
            if (!userId) return;
            applyUserPermissions(userId);
        });

        // Initial Load
        const initialUserId = $('#user_id').val();
        loadPermissionStructure(() => {
            if (initialUserId) {
                applyUserPermissions(initialUserId);
            }
        });
    });

    // Toggle all checkboxes in a menu group
    function toggleMenuPermissions(menuLink, isChecked) {
        $(`.${menuLink}`).prop('checked', isChecked);
        $(`#${menuLink}`).prop('checked', isChecked);
    }
</script>

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
