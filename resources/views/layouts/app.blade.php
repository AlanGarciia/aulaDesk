<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4">

    <title>{{ config('app.name', 'AulaDesk') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

    @stack('styles')
</head>

<body class="font-sans antialiased bg-[var(--color-bg)]">
<div class="min-h-screen">

    <main>
        {{ $slot }}
    </main>

    <x-notification-bell />

</div>

@stack('modals')

@stack('scripts')

@if(session('error_modal'))
<div x-data="{ open: true }"
     x-show="open"
     x-cloak
     class="error-modal-backdrop"
     @keydown.escape.window="open = false">
    <div class="error-modal" @click.outside="open = false">
        <div class="error-modal__icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h3 class="error-modal__title">No es pot fer aquesta acció</h3>
        <p class="error-modal__msg">{{ session('error_modal') }}</p>
        <button type="button" class="error-modal__btn" @click="open = false">D'acord</button>
    </div>
</div>

<style>
    .error-modal-backdrop {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center;
        padding: 16px;
        animation: errorModalFadeIn .15s ease;
    }
    .error-modal {
        background: #fff;
        border-radius: 16px;
        max-width: 420px;
        width: 100%;
        padding: 28px 24px 20px;
        text-align: center;
        box-shadow: 0 24px 60px rgba(0,0,0,0.35);
        animation: errorModalPopIn .2s ease;
        font-family: 'Figtree', system-ui, sans-serif;
    }
    .error-modal__icon {
        font-size: 2.4rem;
        color: #dc3545;
        margin-bottom: 6px;
        line-height: 1;
    }
    .error-modal__title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1c1c1e;
        margin: 0 0 6px;
    }
    .error-modal__msg {
        color: #4b5563;
        font-size: .92rem;
        margin: 0 0 18px;
        line-height: 1.45;
    }
    .error-modal__btn {
        background: #2563eb;
        color: #fff;
        border: 0;
        padding: 10px 22px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: .92rem;
        transition: background .12s;
    }
    .error-modal__btn:hover { background: #1d4ed8; }

    @media (prefers-color-scheme: dark) {
        .error-modal { background: #1f1f22; }
        .error-modal__title { color: #f3f4f6; }
        .error-modal__msg { color: #d1d5db; }
    }

    @keyframes errorModalFadeIn {
        from { opacity: 0; } to { opacity: 1; }
    }
    @keyframes errorModalPopIn {
        from { opacity: 0; transform: scale(.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endif
</body>
</html>
