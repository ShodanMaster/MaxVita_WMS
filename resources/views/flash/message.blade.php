@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash.modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else

        <script>
            var bsMessages = { default:'default', info:'info',warning:'warning', danger:'error',success:'success' }
            Lobibox.notify( bsMessages.{{ $message['level'] }}, {
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
                delay: {{ $message['important'] ? 'true' : '4000' }},
                @if($message['title']) title: '{{  $message['title'] }}', @endif
                msg: '{{ $message['message'] }}'
            });
        </script>

        <!--

        <div class="alert
                    alert-{{ $message['level'] }}
                    {{ $message['important'] ? 'alert-important' : '' }}"
                    role="alert"
        >
            @if ($message['important'])
                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-hidden="true"
                >&times;</button>
            @endif

            {!! $message['message'] !!}
        </div>

        -->
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
