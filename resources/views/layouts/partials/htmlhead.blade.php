<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="web dataeglobal backoffice">
<meta name="author" content="deg.html">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', config('app.name', 'DEG Admin'))</title>
{{ Html::favicon( 'favicon.ico' ) }}
{{ Html::style('fontawesome-free/css/all.min.css') }}
{{ Html::style('css/app.css') }}

@stack('styles')
{{-- Scripts --}}
<script src="{{asset('OneSignalSDKWorker.js')}}"></script>
<script src="{{asset('OneSignalSDKUpdaterWorker.js')}}"></script>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

<script type="text/javascript">
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>
</head>
