<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-eval' 'unsafe-inline' http://localhost:5173 http://localhost:5174; style-src 'self' 'unsafe-inline' https://fonts.bunny.net http://localhost:5173 http://localhost:5174; font-src 'self' https://fonts.bunny.net; img-src 'self' data: https:; connect-src 'self' http://localhost:8000 http://127.0.0.1:8000 http://localhost:8001 http://127.0.0.1:8001 http://localhost:5173 http://localhost:5174 ws://localhost:5173 ws://localhost:5174;">

        <title>{{ config('app.name', 'Armely Store') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo/logo1.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/logo1.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app"></div>
    </body>
</html>
