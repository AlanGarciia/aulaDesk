@php
    $current = app()->getLocale();
    $languages = [
        'ca' => 'Català',
        'es' => 'Castellano',
    ];
@endphp

<div class="lang-switcher" x-data="{ open: false }" @click.outside="open = false">
    <button type="button" class="lang-switcher__btn" @click="open = !open" aria-haspopup="true" :aria-expanded="open">
        <i class="bi bi-translate"></i>
        <span>{{ strtoupper($current) }}</span>
        <i class="bi bi-chevron-down lang-switcher__caret"></i>
    </button>

    <div class="lang-switcher__menu" x-show="open" x-cloak x-transition>
        @foreach($languages as $code => $label)
            <a href="{{ route('locale.switch', $code) }}"
               class="lang-switcher__item {{ $current === $code ? 'is-active' : '' }}">
                <span>{{ $label }}</span>
                @if($current === $code)
                    <i class="bi bi-check2"></i>
                @endif
            </a>
        @endforeach
    </div>
</div>

<style>
    .lang-switcher {
        position: relative;
        font-family: 'Figtree', system-ui, sans-serif;
    }
    .lang-switcher__btn {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 10px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: .88rem;
        font-weight: 600;
        color: #1c1c1e;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
        transition: background .12s;
    }
    .lang-switcher__btn:hover { background: #f3f4f6; }
    .lang-switcher__caret { font-size: .7rem; opacity: .6; }

    .lang-switcher__menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        min-width: 150px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.18);
        overflow: hidden;
        z-index: 9999;
    }
    .lang-switcher__item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        text-decoration: none;
        color: #1c1c1e;
        font-size: .88rem;
        transition: background .12s;
    }
    .lang-switcher__item:hover { background: #f3f4f6; }
    .lang-switcher__item.is-active {
        font-weight: 700;
        color: #2563eb;
    }
    .lang-switcher__item .bi-check2 { color: #2563eb; }

    [x-cloak] { display: none !important; }

    @media (prefers-color-scheme: dark) {
        .lang-switcher__btn { background: #1f1f22; color: #f3f4f6; border-color: rgba(255,255,255,.1); }
        .lang-switcher__btn:hover { background: #2a2a2e; }
        .lang-switcher__menu { background: #1f1f22; }
        .lang-switcher__item { color: #f3f4f6; }
        .lang-switcher__item:hover { background: #2a2a2e; }
    }
</style>