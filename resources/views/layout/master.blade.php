<!DOCTYPE html>
<html>

<head>
    <title>Warehouse Management System</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    {{-- <meta name="_token" content="{{ csrf_token() }}"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <script src="{{asset('assets/js/general.js')}}"></script> -->


    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/favicon.ico') }}">


    <!-- plugin css -->
    <link href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <!-- end plugin css -->

    @stack('plugin-styles')

    <!-- common css -->
    @if ($darkmode == 0)
        <link href="{{ asset('css/app-light.css') }}" rel="stylesheet" />
    @else
        <link href="{{ asset('css/app-dark.css') }}" rel="stylesheet" />
    @endif

    <!-- end common css -->

    <!-- lobibox notification -->
    <link rel="stylesheet" href="{{ asset('components/lobibox/dist/css/lobibox.min.css') }}">

    @stack('style')
</head>

<body data-base-url="{{ url('/') }}">

    <!-- jQuery 3 -->
    <script src="{{ asset('components/jquery/jquery-3.6.0.min.js') }}"></script>

    <!-- lobibox notification -->
    <script src="{{ asset('components/lobibox/dist/js/notifications.min.js') }}"></script>

    <div class="main-wrapper" id="app">
        @include('layout.sidebar')
        <div class="page-wrapper">
            @include('layout.header')
            @include('flash.message')
            <div class="page-content">
                @yield('content')
            </div>
            @include('layout.footer')
        </div>
    </div>

    <!-- base js -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <!-- end common js -->

    <script>
    $(document).ready(function() {
    //     $.ajax({
    //         headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    //                 },
    //         url: "{{url('/ajax/favicon')}}",
    //         type: 'POST',
    //         dataType: 'json',
    //         success: function(response) {

    //         var favicon = response;

    //         $('#favicon').attr('href', '{{ asset('dist/img/') }}' + '/' + favicon);
    //         }
    //     });

        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('.expiry-date').forEach(function(input) {
            input.setAttribute('min', today);
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function sweetAlertMessage(type, title, message, autoClose = true, redirectUrl = null) {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK',
            showConfirmButton: !autoClose,
            timer: autoClose ? 1000 : null,
            timerProgressBar: autoClose
        }).then((result) => {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });
    }

    </script>

    <!-- custom-scripts js -->
    @stack('custom-scripts')
    <!-- end custom-scripts js -->
    @if (!is_null(session()->get('contents')))
        <script>
            console.log('qwertyuiop');

            window.open("{{ route('printbarcode') }}");
        </script>
    @endif

</body>

</html>
