@extends('layout.master2')

@section('content')
@if(isset($config->back_img))
<div class="page-content d-flex align-items-center justify-content-center" style="background-image: url('{{ asset("dist/img/$config->back_img") }}'); background-size: cover;">
@else
<div class="page-content d-flex align-items-center justify-content-center">
@endif

  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-8 col-xl-6 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-12">
            <div class="auth-form-wrapper px-4 py-5">
              <div class="text-center">
                  <img src="{{ asset('assets/images/logo/maxvita-logo-small.png') }}" alt="Login Logo" width="100px" />
                  <a href="#" class="noble-ui-logo d-block mb-2">Warehouse Management System</a>
              </div>

              <form method="POST" action="{{ route('login.store') }}">
                @csrf

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <i class="fa fa-info" style="margin-right:0.5em;"></i>
                  @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                  @endforeach
                </div>
                @endif

                <div class="form-group row">
                  <div class="col-lg-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i data-feather="user" class="icon-md"></i></span>
                      </div>
                      <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        class="form-control"
                        placeholder="Username"
                        autofocus>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-lg-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i data-feather="key" class="icon-md"></i></span>
                      </div>
                      <input
                        type="password"
                        class="form-control"
                        name="password"
                        id="password"
                        autocomplete="current-password"
                        placeholder="Password">
                    </div>
                  </div>
                </div>

                <div class="form-check form-check-flat form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="rememberMe" checked>
                    Remember me
                  </label>
                </div>

                <div class="mt-3 text-center">
                  <button type="submit" class="btn btn-primary px-4">Login</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
