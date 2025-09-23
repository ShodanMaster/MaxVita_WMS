<!DOCTYPE html>
<html>
<head>
  <title>Warehouse Management System</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="_token" content="{{ csrf_token() }}">


  @if(isset($config->favi))
  <link rel="shortcut icon" href='{{ asset("dist/img/$config->favi") }}'>
  @else
  <link rel="shortcut icon">
  @endif
  <!-- plugin css -->
  <link href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
  <!-- end plugin css -->

  <!-- lobibox notification -->
  <link rel="stylesheet" href="{{asset('components/lobibox/dist/css/lobibox.min.css')}}">

  @stack('plugin-styles')

  <!-- common css -->
  <link href="{{ asset('css/app-light.css') }}" rel="stylesheet" />
  <!-- end common css -->


  @stack('style')
</head>

<body data-base-url="{{url('/')}}" >

  <script src="{{ asset('assets/js/spinner.js') }}"></script>

  <div class="main-wrapper" id="app">
    <div class="page-wrapper full-page">
      @yield('content')
    </div>
  </div>

    <!-- base js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <!-- end common js -->

  <!-- lobibox notification -->
  <script src="{{asset('components/lobibox/dist/js/notifications.min.js')}}"></script>
  <script>
            var bsMessages = { default:'default', info:'info',warning:'warning', danger:'error',success:'success' }
            Lobibox.notify( success, {
                pauseDelayOnHover: true, //  only if continueDelayOnInactiveTab is false.
                continueDelayOnInactiveTab: false,
                delayIndicator: true,
                position: 'top right',
                sound: false,
                showClass: 'fadeInDown',
                hideClass: 'fadeUpDown',
                icon: true,
                closeOnClick: true,
                iconSource: "bootstrap", //fontAwesome
                size: 'mini',
                delay:  '4000',
                title: 'Title',
                msg: 'Message comes here'
            });
        </script>

    @stack('custom-scripts')


</body>
</html>
