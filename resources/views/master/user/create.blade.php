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
                        @if (isset($user))
                            Update User
                        @else
                            Add User
                        @endif
                    </h5>

                    <form
                        action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="form-horizontal validate"
                        autocomplete="off"
                    >
                        @csrf
                        @if (isset($user))
                            @method('PATCH')
                        @endif

                        <!-- User Form Fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">
                                        Name <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('name', $user->name ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="username" class="col-sm-4 control-label">
                                        Username <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="text"
                                        name="username"
                                        id="username"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('username', $user->username ?? '') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- User Name & Address -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="user_type" class="col-sm-4 control-label">
                                        User Type <font color="#FF0000">*</font>
                                    </label>
                                    <select name="user_type" class="form-control form-control-sm">
                                        <option value="1" {{ old('user_type', $user->user_type ?? '') == '1' ? 'selected' : '' }}>Standard User</option>
                                        <option value="2" {{ old('user_type', $user->user_type ?? '') == '2' ? 'selected' : '' }}>Administrator</option>
                                    </select>
                                </div>
                            </div>

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
                                                {{ old('location_id', $user->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="email" class="col-sm-4 control-label">
                                        Email <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        required
                                        class="form-control form-control-sm"
                                        value="{{ old('email', $user->email ?? '') }}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="permission_level" class="col-sm-4 control-label">
                                        Permission Level <font color="#FF0000">*</font>
                                    </label>
                                    <select name="permission_level" class="form-control form-control-sm" id="permission_level">
                                        <option value="1" {{ old('permission_level', $user->permission_level ?? '') == '1' ? 'selected' : '' }}>Top Level</option>
                                        <option value="2" {{ old('permission_level', $user->permission_level ?? '') == '2' ? 'selected' : '' }}>Organization Level</option>
                                        <option value="3" {{ old('permission_level', $user->permission_level ?? '') == '3' ? 'selected' : '' }}>Location Level</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="password" class="col-sm-4 control-label">
                                        Password <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control form-control-sm"
                                        {{ isset($user) ? 'disabled' : 'required' }}
                                        {{ isset($user) ? 'readonly' : '' }}
                                    >
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="permission_level" class="col-sm-4 control-label">
                                        Confirm Password <font color="#FF0000">*</font>
                                    </label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="form-control form-control-sm"
                                        {{ isset($user) ? 'disabled' : 'required' }}
                                        {{ isset($user) ? 'readonly' : '' }}
                                    >
                                </div>
                            </div>

                            <!-- Checkbox to toggle the password field -->
                            @if (isset($user))

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="toggle_password" class="col-sm-4 control-label">
                                            Enable Password Fields
                                        </label>
                                        <input
                                            type="checkbox"
                                            id="toggle_password"
                                            class="form-check-input"
                                        >
                                        <label for="toggle_password" class="form-check-label">Click to Enable Password</label>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($user) ? 'Update' : 'Add' }}
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
    document.getElementById('toggle_password').addEventListener('change', function() {
        var passwordField = document.getElementById('password');
        var confirmPasswordField = document.getElementById('password_confirmation');

        if (this.checked) {
            passwordField.removeAttribute('disabled');
            passwordField.removeAttribute('readonly');
            confirmPasswordField.removeAttribute('disabled');
            confirmPasswordField.removeAttribute('readonly');
            passwordField.setAttribute('required', 'required');
            confirmPasswordField.setAttribute('required', 'required');
        } else {
            passwordField.setAttribute('disabled', 'disabled');
            passwordField.setAttribute('readonly', 'readonly');
            confirmPasswordField.setAttribute('disabled', 'disabled');
            confirmPasswordField.setAttribute('readonly', 'readonly');
            passwordField.removeAttribute('required');
            confirmPasswordField.removeAttribute('required');
        }
    });
</script>
@endpush
