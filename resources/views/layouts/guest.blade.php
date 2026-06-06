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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <!-- CSS / Vite desde los Blades -->
    @stack('styles')
</head>
<body>
    <div style="position:fixed; top:16px; right:16px; z-index:50; background:rgba(255,255,255,.9); padding:6px 10px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08);">
        <x-language-switcher />
    </div>
    <div class="login-page">
        {{ $slot }}
    </div>

    <!-- JS / Vite desde los Blades -->
    @stack('scripts')
</body>
</html>