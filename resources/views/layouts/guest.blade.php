<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4">

    <title>{{ config('app.name', 'aulaDesk') }}</title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS / Vite desde los Blades -->
    @stack('styles')

    <style>
        .lang-float { position: fixed; top: 16px; right: 16px; z-index: 100; }
        .lang-switcher { position: relative; font-family: 'Figtree', system-ui, sans-serif; }
        .lang-switcher__btn {
            display: flex; align-items: center; gap: 6px;
            background: #fff; border: 1px solid rgba(0,0,0,.08);
            border-radius: 10px; padding: 8px 12px; cursor: pointer;
            font-size: .85rem; font-weight: 600; color: #1d2433;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }
        .lang-switcher__btn:hover { background: #f3f4f6; }
        .lang-switcher__caret { font-size: .7rem; opacity: .6; }
        .lang-switcher__menu {
            position: absolute; top: calc(100% + 6px); right: 0;
            min-width: 150px; background: #fff; border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.16); overflow: hidden;
        }
        .lang-switcher__item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px; text-decoration: none; color: #1d2433;
            font-size: .85rem;
        }
        .lang-switcher__item:hover { background: #f3f4f6; }
        .lang-switcher__item.is-active { font-weight: 700; color: #2563eb; }
        .lang-switcher__item .bi-check2 { color: #2563eb; }
    </style>
</head>
<body>

    {{-- Selector d'idioma --}}
    <div class="lang-float">
        @php
            $current = app()->getLocale();
            $languages = ['ca' => 'Català', 'es' => 'Castellano'];
        @endphp
        <div class="lang-switcher" id="langSwitcher">
            <button type="button" class="lang-switcher__btn" id="langSwitcherBtn">
                <i class="bi bi-translate"></i>
                <span>{{ strtoupper($current) }}</span>
                <i class="bi bi-chevron-down lang-switcher__caret"></i>
            </button>
            <div class="lang-switcher__menu" id="langSwitcherMenu" style="display:none;">
                @foreach($languages as $code => $label)
                    <a href="{{ route('locale.switch', $code) }}"
                       class="lang-switcher__item {{ $current === $code ? 'is-active' : '' }}">
                        <span>{{ $label }}</span>
                        @if($current === $code)<i class="bi bi-check2"></i>@endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="login-page">
        {{ $slot }}
    </div>

    <!-- JS / Vite desde los Blades -->
    @stack('scripts')

    <script>
        (function () {
            const wrapper = document.getElementById('langSwitcher');
            const btn = document.getElementById('langSwitcherBtn');
            const menu = document.getElementById('langSwitcherMenu');
            if (!btn) return;

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', function (e) {
                if (!wrapper.contains(e.target)) menu.style.display = 'none';
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') menu.style.display = 'none';
            });
        })();
    </script>
</body>
</html>