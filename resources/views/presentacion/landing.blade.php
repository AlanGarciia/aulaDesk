<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>aulaDesk · {{ __('messages.landing_welcome') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --ink: #1d2433;
            --ink-soft: #4b5566;
            --blue: #2563eb;
            --blue-deep: #1e40af;
            --coral: #f0653f;
            --amber: #f5a524;
            --paper: #fbfaf6;
            --line: rgba(37, 99, 235, .08);
            --card: #ffffff;
            --radius: 18px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Figtree', system-ui, sans-serif;
            color: var(--ink);
            background: var(--paper);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Fondo cuaderno: cuadrícula tenue + margen rojo de libreta */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--line) 1px, transparent 1px),
                linear-gradient(90deg, var(--line) 1px, transparent 1px);
            background-size: 28px 28px;
            z-index: -2;
            pointer-events: none;
        }
        body::after {
            content: "";
            position: fixed;
            top: 0; bottom: 0; left: 64px;
            width: 2px;
            background: rgba(240, 101, 63, .18);
            z-index: -1;
            pointer-events: none;
        }

        h1, h2, h3 { font-family: 'Fraunces', Georgia, serif; line-height: 1.15; }

        .container { max-width: 1080px; margin: 0 auto; padding: 0 24px; }

        /* ---- Selector idioma flotante ---- */
        .lang-float {
            position: fixed;
            top: 18px; right: 18px;
            z-index: 100;
        }
        .lang-switcher { position: relative; font-family: 'Figtree', sans-serif; }
        .lang-switcher__btn {
            display: flex; align-items: center; gap: 6px;
            background: #fff; border: 1px solid rgba(0,0,0,.08);
            border-radius: 10px; padding: 8px 12px; cursor: pointer;
            font-size: .85rem; font-weight: 600; color: var(--ink);
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
            padding: 10px 14px; text-decoration: none; color: var(--ink);
            font-size: .85rem;
        }
        .lang-switcher__item:hover { background: #f3f4f6; }
        .lang-switcher__item.is-active { font-weight: 700; color: var(--blue); }
        [x-cloak] { display: none !important; }

        /* ---- HERO ---- */
        .hero {
            position: relative;
            text-align: center;
            padding: 110px 24px 80px;
        }
        .kicker {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(37, 99, 235, .08);
            color: var(--blue-deep);
            font-weight: 600; font-size: .82rem;
            padding: 7px 16px; border-radius: 999px;
            margin-bottom: 22px;
            text-transform: uppercase; letter-spacing: .04em;
        }
        .hero-logo {
            width: 76px; height: 76px; margin: 0 auto 20px;
            display: grid; place-items: center;
            background: #fff; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(37,99,235,.14);
        }
        .hero-logo img { width: 70%; height: auto; }
        .hero h1 {
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            font-weight: 700; color: var(--ink);
            margin-bottom: 18px;
        }
        .hero h1 .brand {
            color: var(--blue);
            position: relative;
            white-space: nowrap;
        }
        /* subrayado dibujado a mano */
        .hero h1 .brand::after {
            content: "";
            position: absolute; left: 0; right: 0; bottom: -6px;
            height: 8px;
            background: var(--amber);
            border-radius: 6px;
            opacity: .55;
            transform: rotate(-.5deg);
        }
        .hero .lead {
            max-width: 600px; margin: 0 auto 36px;
            font-size: 1.18rem; color: var(--ink-soft);
        }
        .actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 26px; border-radius: 12px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none; cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            border: 2px solid transparent;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn--primary {
            background: var(--blue); color: #fff;
            box-shadow: 0 8px 22px rgba(37,99,235,.30);
        }
        .btn--primary:hover { box-shadow: 0 12px 28px rgba(37,99,235,.40); }
        .btn--ghost {
            background: #fff; color: var(--ink);
            border-color: rgba(29,36,51,.12);
        }
        .hero-status {
            margin-top: 30px; font-size: .85rem; color: var(--ink-soft);
            display: inline-flex; align-items: center; gap: 8px;
        }
        .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e; box-shadow: 0 0 0 4px rgba(34,197,94,.18);
        }

        /* ---- Secciones ---- */
        .section { padding: 70px 0; }
        .section-head { text-align: center; max-width: 640px; margin: 0 auto 48px; }
        .section-head h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.6rem);
            margin-bottom: 14px; color: var(--ink);
        }
        .section-head p { color: var(--ink-soft); font-size: 1.08rem; }

        /* ---- Features (tarjetas con icono) ---- */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }
        .feature {
            background: var(--card);
            border: 1px solid rgba(29,36,51,.07);
            border-radius: var(--radius);
            padding: 28px 26px;
            transition: transform .18s, box-shadow .18s;
        }
        .feature:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(29,36,51,.10);
        }
        .feature__icon {
            width: 50px; height: 50px; border-radius: 14px;
            display: grid; place-items: center;
            font-size: 1.5rem; margin-bottom: 16px;
        }
        .feature:nth-child(1) .feature__icon { background: rgba(37,99,235,.12); color: var(--blue); }
        .feature:nth-child(2) .feature__icon { background: rgba(245,165,36,.16); color: #b8780b; }
        .feature:nth-child(3) .feature__icon { background: rgba(240,101,63,.13); color: var(--coral); }
        .feature:nth-child(4) .feature__icon { background: rgba(34,197,94,.13); color: #16a34a; }
        .feature:nth-child(5) .feature__icon { background: rgba(139,92,246,.13); color: #7c3aed; }
        .feature:nth-child(6) .feature__icon { background: rgba(37,99,235,.12); color: var(--blue); }
        .feature h3 { font-size: 1.2rem; margin-bottom: 8px; }
        .feature p { color: var(--ink-soft); font-size: .96rem; }

        /* ---- Bloque destacado (tipo pizarra) ---- */
        .about {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            border-radius: 26px;
            color: #fff;
            text-align: center;
            padding: 64px 36px;
            position: relative;
            overflow: hidden;
        }
        .about::before {
            content: "";
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        .about h2 { color: #fff; position: relative; font-size: clamp(1.7rem, 3.5vw, 2.4rem); margin-bottom: 14px; }
        .about p { color: rgba(255,255,255,.85); position: relative; max-width: 600px; margin: 0 auto; font-size: 1.1rem; }

        /* ---- Planes ---- */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px; max-width: 760px; margin: 0 auto;
        }
        .plan {
            background: #fff; border: 1px solid rgba(29,36,51,.08);
            border-radius: var(--radius); padding: 32px 28px;
            position: relative;
        }
        .plan--featured {
            border-color: var(--blue);
            box-shadow: 0 18px 44px rgba(37,99,235,.16);
        }
        .plan__badge {
            position: absolute; top: -13px; left: 50%; transform: translateX(-50%);
            background: var(--coral); color: #fff;
            font-size: .76rem; font-weight: 700;
            padding: 6px 16px; border-radius: 999px;
            text-transform: uppercase; letter-spacing: .03em;
        }
        .plan__title { font-size: 1.5rem; margin-bottom: 4px; }
        .plan__subtitle { color: var(--ink-soft); margin-bottom: 20px; font-size: .95rem; }
        .plan__features { list-style: none; margin-bottom: 26px; }
        .plan__features li {
            padding: 8px 0; display: flex; align-items: center; gap: 10px;
            color: var(--ink-soft); font-size: .96rem;
            border-bottom: 1px dashed rgba(29,36,51,.10);
        }
        .plan__features li i { color: #16a34a; }
        .plan__btn {
            display: block; text-align: center;
            padding: 13px; border-radius: 11px;
            text-decoration: none; font-weight: 600;
            background: #f1f3f7; color: var(--ink);
            transition: background .15s;
        }
        .plan__btn:hover { background: #e5e8ef; }
        .plan__btn--primary { background: var(--blue); color: #fff; }
        .plan__btn--primary:hover { background: var(--blue-deep); }

        /* ---- Footer ---- */
        .footer {
            background: #11182a; color: #cbd3e1;
            padding: 48px 0 24px; margin-top: 40px;
            text-align: center;
        }
        .footer-simple { margin-bottom: 28px; }
        .footer h3 { color: #fff; font-size: 1.5rem; margin-bottom: 10px; }
        .footer-simple p {
            font-size: .95rem; color: #94a0b8;
            max-width: 460px; margin: 0 auto;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.08);
            padding-top: 22px;
            font-size: .85rem; color: #94a0b8;
        }
        .footer-bottom--center { text-align: center; }
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

    {{-- HERO --}}
    <section class="hero">
        <div class="container">
            <div class="hero-logo">
                <img src="{{ asset('img/logo_solo.png') }}" alt="Logo aulaDesk">
            </div>
            <div class="kicker"><i class="bi bi-mortarboard-fill"></i> {{ __('messages.landing_kicker') }}</div>
            <h1>{{ __('messages.landing_welcome') }} <span class="brand">aulaDesk</span></h1>
            <p class="lead">{{ __('messages.landing_lead') }}</p>
            <div class="actions">
                <a class="btn btn--primary" href="{{ route('register') }}">
                    {{ __('messages.landing_start') }} <span>→</span>
                </a>
                <a class="btn btn--ghost" href="{{ route('login') }}">
                    {{ __('messages.landing_have_account') }} <span>→</span>
                </a>
            </div>
            <div class="hero-status">
                <span class="dot"></span> {{ __('messages.landing_secure') }}
            </div>
        </div>
    </section>

    {{-- BLOQUE PIZARRA --}}
    <div class="container">
        <section class="about">
            <h2>{{ __('messages.landing_about_title') }}</h2>
            <p>{{ __('messages.landing_about_text') }}</p>
        </section>
    </div>

    {{-- FEATURES --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>{{ __('messages.landing_features_title') }}</h2>
            </div>
            <div class="feature-grid">
                <div class="feature">
                    <div class="feature__icon"><i class="bi bi-hand-thumbs-up-fill"></i></div>
                    <h3>{{ __('messages.feat1_title') }}</h3>
                    <p>{{ __('messages.feat1_text') }}</p>
                </div>
                <div class="feature">
                    <div class="feature__icon"><i class="bi bi-lightning-charge-fill"></i></div>
                    <h3>{{ __('messages.feat2_title') }}</h3>
                    <p>{{ __('messages.feat2_text') }}</p>
                </div>
                <div class="feature">
                    <div class="feature__icon"><i class="bi bi-bullseye"></i></div>
                    <h3>{{ __('messages.feat3_title') }}</h3>
                    <p>{{ __('messages.feat3_text') }}</p>
                </div>
                <div class="feature">
                    <div class="feature__icon"><i class="bi bi-shield-check"></i></div>
                    <h3>{{ __('messages.feat4_title') }}</h3>
                    <p>{{ __('messages.feat4_text') }}</p>
                </div>
                <div class="feature">
                    <div class="feature__icon"><i class="bi bi-arrow-repeat"></i></div>
                    <h3>{{ __('messages.feat5_title') }}</h3>
                    <p>{{ __('messages.feat5_text') }}</p>
                </div>
                <div class="feature">
                    <div class="feature__icon"><i class="bi bi-people-fill"></i></div>
                    <h3>{{ __('messages.feat6_title') }}</h3>
                    <p>{{ __('messages.feat6_text') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- PLANES --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>{{ __('messages.landing_pricing_title') }}</h2>
                <p>{{ __('messages.landing_pricing_sub') }}</p>
            </div>
            <div class="pricing-grid">
                <div class="plan">
                    <h3 class="plan__title">{{ __('messages.plan_free') }}</h3>
                    <p class="plan__subtitle">{{ __('messages.plan_free_subtitle') }}</p>
                    <ul class="plan__features">
                        <li><i class="bi bi-check-lg"></i> {{ __('messages.plan_free_f1') }}</li>
                        <li><i class="bi bi-check-lg"></i> {{ __('messages.plan_free_f2') }}</li>
                        <li><i class="bi bi-check-lg"></i> {{ __('messages.plan_free_f3') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="plan__btn">{{ __('messages.plan_free_btn') }}</a>
                </div>
                <div class="plan plan--featured">
                    <div class="plan__badge">{{ __('messages.plan_most_popular') }}</div>
                    <h3 class="plan__title">Premium</h3>
                    <p class="plan__subtitle">{{ __('messages.plan_premium_subtitle') }}</p>
                    <ul class="plan__features">
                        <li><i class="bi bi-check-lg"></i> {{ __('messages.plan_premium_f1') }}</li>
                        <li><i class="bi bi-check-lg"></i> {{ __('messages.plan_premium_f2') }}</li>
                        <li><i class="bi bi-check-lg"></i> {{ __('messages.plan_premium_f3') }}</li>
                    </ul>
                    <a href="{{ route('stripe.checkout.premium') }}" class="plan__btn plan__btn--primary">{{ __('messages.plan_premium_btn') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-simple">
                <h3>aulaDesk</h3>
                <p>{{ __('messages.footer_tagline') }}</p>
            </div>
            <div class="footer-bottom footer-bottom--center">
                <span>© {{ date('Y') }} aulaDesk · {{ __('messages.all_rights_reserved') }}</span>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const wrapper = document.getElementById('langSwitcher');
            const btn = document.getElementById('langSwitcherBtn');
            const menu = document.getElementById('langSwitcherMenu');

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