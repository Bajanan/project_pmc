<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/fav.png') }}">

    {{-- <link rel="stylesheet" href="{{ asset('build/assets/main-sKYFLesP.css') }}"> --}}

    <!-- Scripts -->
    @vite(['resources/sass/main.scss'])
    @vite(['resources/sass/app.scss', 'resources/js/app.js'  ])
</head>
<body>
    <div id="app" style="min-height: 100vh;">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
