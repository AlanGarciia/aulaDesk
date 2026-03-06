<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fuente -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS / Vite desde los Blades -->
    @stack('styles')
</head>
<body>
    <div class="login-page">
        {{ $slot }}
    </div>

    <!-- JS / Vite desde los Blades -->
    @stack('scripts')
</body>
</html>