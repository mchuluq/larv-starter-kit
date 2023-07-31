<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{env('APP_URL')}}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ url('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js','resources/js/customs/theme-switcher.js'])

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script src="{{ Vite::asset('resources/js/vendor/webauthn/webauthn.js') }}"></script>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div class="c-auth-layout d-flex align-items-center">
        @yield('content')
    </div>
</body>
</html>