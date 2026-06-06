<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>aulaDesk · {{ __('messages.plans') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --night-0: #0f1729;
            --night-1: #0b1120;
            --panel: #1a2236;
            --line: rgba(255,255,255,.08);
            --text: #e7ebf3;
            --muted: #9aa6bd;
            --muted2: #6b7689;
            --blue: #3b82f6;
            --blue-deep: #2563eb;
            --amber: #f5a524;
            --coral: #f0653f;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* Sticky footer: body en columna a altura completa */
        html, body { height: 100%; }
        body {
            font-family: 'Figtree', system-ui, sans-serif;
            color: var(--text);
            background: linear-gradient(180deg, var(--night-0), var(--night-1));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }
        body::before {
            content: "";
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
            z-index: 0;
        }

        h1, h2, h3 { font-family: 'Fraunces', Georgia, serif; line-height: 1.15; }

        /* El contenido crece y empuja el footer abajo */
        .page-content { flex: 1 0 auto; position: relative; z-index: 1; }

        /* Botó tornar */
        .back-btn {
            position: fixed; top: 20px; left: 20px; z-index: 100;
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px;
            background: var(--panel); color: var(--text);
            border: 1px solid var(--line);
            border-radius: 11px;
            text-decoration: none; font-weight: 600; font-size: .9rem;
            cursor: pointer;
            transition: transform .15s, background .15s, border-color .15s;
        }
        .back-btn:hover { transform: translateX(-2px); background: #202a42; border-color: rgba(255,255,255,.14); }

        /* Selector idioma */
        .lang-float { position: fixed; top: 20px; right: 20px; z-index: 100; }
        .lang-switcher { position: relative; }
        .lang-switcher__btn {
            display: flex; align-items: center; gap: 6px;
            background: var(--panel); border: 1px solid var(--line);
            border-radius: 10px; padding: 8px 12px; cursor: pointer;
            font-size: .85rem; font-weight: 600; color: var(--text);
        }
        .lang-switcher__btn:hover { background: #202a42; }
        .lang-switcher__caret { font-size: .7rem; opacity: .6; }
        .lang-switcher__menu {
            position: absolute; top: calc(100% + 6px); right: 0;
            min-width: 150px; background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px; box-shadow: 0 16px 44px rgba(0,0,0,.5); overflow: hidden;
        }
        .lang-switcher__item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px; text-decoration: none; color: var(--text);
            font-size: .85rem;
        }
        .lang-switcher__item:hover { background: rgba(255,255,255,.06); }
        .lang-switcher__item.is-active { font-weight: 700; color: var(--blue); }
        .lang-switcher__item .bi-check2 { color: var(--blue); }

        /* Cabecera */
        .pricing-head {
            text-align: center;
            padding: 110px 24px 20px;
            max-width: 640px; margin: 0 auto;
        }
        .pricing-head .kicker {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(59,130,246,.14); color: #bfdbfe;
            font-weight: 600; font-size: .82rem;
            padding: 7px 16px; border-radius: 999px; margin-bottom: 18px;
            text-transform: uppercase; letter-spacing: .04em;
        }
        .pricing-head h1 { font-size: clamp(2rem, 4.5vw, 3rem); color: #fff; margin-bottom: 12px; }
        .pricing-head p { color: var(--muted); font-size: 1.1rem; }

        /* Grid de planes */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            max-width: 760px;
            margin: 30px auto 70px;
            padding: 0 24px;
        }
        .plan-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 34px 30px;
            position: relative;
            overflow: hidden;
            transition: transform .18s, border-color .18s, box-shadow .18s;
        }
        .plan-card::before {
            content: ""; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: var(--muted2);
        }
        .plan-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255,255,255,.16);
            box-shadow: 0 18px 44px rgba(0,0,0,.4);
        }
        .plan-card.plan-featured {
            border-color: rgba(59,130,246,.45);
            box-shadow: 0 18px 44px rgba(0,0,0,.4);
        }
        .plan-card.plan-featured::before { background: var(--blue); }

        .badge {
            position: absolute; top: 14px; right: 14px;
            background: var(--coral); color: #fff;
            font-size: .72rem; font-weight: 700;
            padding: 5px 12px; border-radius: 999px;
            text-transform: uppercase; letter-spacing: .03em;
        }
        .plan-title { font-size: 1.6rem; margin-bottom: 4px; color: #fff; }
        .plan-subtitle { color: var(--muted); margin-bottom: 22px; font-size: .96rem; }
        .plan-features { list-style: none; margin-bottom: 28px; }
        .plan-features li {
            padding: 9px 0; display: flex; align-items: center; gap: 10px;
            color: var(--muted); font-size: .96rem;
            border-bottom: 1px dashed rgba(255,255,255,.08);
        }
        .plan-features li::before {
            content: "\F26E";
            font-family: "bootstrap-icons";
            color: #4ade80; font-size: .9rem;
        }
        .plan-btn {
            display: block; text-align: center;
            padding: 13px; border-radius: 11px;
            text-decoration: none; font-weight: 600;
            background: rgba(255,255,255,.06); color: var(--text);
            border: 1px solid var(--line);
            transition: background .15s, transform .15s;
        }
        .plan-btn:hover { background: rgba(255,255,255,.10); transform: translateY(-1px); }
        .plan-btn.primary {
            background: var(--blue); color: #fff; border-color: transparent;
            box-shadow: 0 8px 22px rgba(59,130,246,.30);
        }
        .plan-btn.primary:hover { background: var(--blue-deep); }

        /* Footer pegado abajo */
        .footer-pro {
            flex-shrink: 0;
            position: relative; z-index: 1;
            background: var(--night-1);
            border-top: 1px solid var(--line);
            color: var(--muted2);
            text-align: center;
            padding: 22px 24px;
            font-size: .85rem;
        }
    </style>
</head>

<body>

    {{-- Botó tornar --}}
    <a href="#" class="back-btn" onclick="event.preventDefault(); history.back();">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>

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

    {{-- CONTENIDO (empuja el footer abajo) --}}
    <div class="page-content">

        <header class="pricing-head">
            <div class="kicker"><i class="bi bi-mortarboard-fill"></i> {{ __('messages.plans') }}</div>
            <h1>{{ __('messages.landing_pricing_title') }}</h1>
            <p>{{ __('messages.landing_pricing_sub') }}</p>
        </header>

        <section class="pricing-grid">

            <div class="plan-card">
                <h3 class="plan-title">{{ __('messages.plan_free') }}</h3>
                <p class="plan-subtitle">{{ __('messages.plan_free_subtitle') }}</p>
                <ul class="plan-features">
                    <li>{{ __('messages.plan_free_f1') }}</li>
                    <li>{{ __('messages.plan_free_f2') }}</li>
                    <li>{{ __('messages.plan_free_f3') }}</li>
                </ul>
                <a href="{{ route('register') }}" class="plan-btn">{{ __('messages.plan_free_btn') }}</a>
            </div>

            <div class="plan-card plan-featured">
                <div class="badge">{{ __('messages.plan_most_popular') }}</div>
                <h3 class="plan-title">Premium</h3>
                <p class="plan-subtitle">{{ __('messages.plan_premium_subtitle') }}</p>
                <ul class="plan-features">
                    <li>{{ __('messages.plan_premium_f1') }}</li>
                    <li>{{ __('messages.plan_premium_f2') }}</li>
                    <li>{{ __('messages.plan_premium_f3') }}</li>
                </ul>
                <a href="{{ route('stripe.checkout.premium') }}" class="plan-btn primary">{{ __('messages.plan_premium_btn') }}</a>
            </div>

        </section>

    </div>

    {{-- Footer pegado abajo --}}
    <footer class="footer-pro">
        © {{ date('Y') }} aulaDesk · {{ __('messages.all_rights_reserved') }}
    </footer>

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