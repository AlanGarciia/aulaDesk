<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4">

    <title>{{ config('app.name', 'AulaDesk') }}</title>

    <!-- Fuente -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS principal -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSS específico de cada vista -->
    @stack('styles')
</head>
<body class="font-sans antialiased">

<div class="min-h-screen">

    {{-- HEADER --}}
    @isset($header)
        <header class="bg-white/80 backdrop-blur shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- CONTENIDO --}}
    <main>
        {{ $slot }}
    </main>
</div>

{{-- Modales específicos de cada vista --}}
@stack('modals')

{{-- Scripts específicos de cada vista --}}
@stack('scripts')

</body>
</html>
