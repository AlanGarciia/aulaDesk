<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>aulaDesk · {{ __('messages.plans') }}</title>

    @vite(['resources/css/presentacion/welcome.css'])

    <style>
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            transition: background 0.2s, transform 0.2s, border-color 0.2s;
            z-index: 100;
        }
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateX(-2px);
        }
        .back-btn svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 540px) {
            .back-btn {
                top: 12px;
                left: 12px;
                padding: 8px 12px;
                font-size: .85rem;
            }
        }
    </style>
</head>

<body>

    <a href="{{ url()->previous() }}" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        {{ __('messages.back') }}
    </a>

    {{-- Selector d'idioma --}}
    <div style="position:fixed; top:20px; right:20px; z-index:100; background:rgba(255,255,255,.1); padding:6px 10px; border-radius:12px; backdrop-filter:blur(10px);">
        <x-language-switcher />
    </div>


    <!-- ============================
        SECCIÓN — PLANS
    ============================= -->
    <section class="section pricing">
        <div class="pricing-grid">

            <!-- PLAN GRATUIT -->
            <div class="plan-card">
                <h3 class="plan-title">{{ __('messages.plan_free') }}</h3>
                <p class="plan-subtitle">{{ __('messages.plan_free_subtitle') }}</p>

                <ul class="plan-features">
                    <li>{{ __('messages.plan_free_f1') }}</li>
                    <li>{{ __('messages.plan_free_f2') }}</li>
                    <li>{{ __('messages.plan_free_f3') }}</li>
                </ul>

                <a href="{{ route('register') }}" class="plan-btn">
                    {{ __('messages.plan_free_btn') }}
                </a>
            </div>

            <!-- PLAN PREMIUM DESTACADO -->
            <div class="plan-card plan-featured">
                <div class="badge">{{ __('messages.plan_most_popular') }}</div>

                <h3 class="plan-title">Premium</h3>
                <p class="plan-subtitle">{{ __('messages.plan_premium_subtitle') }}</p>

                <ul class="plan-features">
                    <li>{{ __('messages.plan_premium_f1') }}</li>
                    <li>{{ __('messages.plan_premium_f2') }}</li>
                    <li>{{ __('messages.plan_premium_f3') }}</li>
                </ul>

                <a href="{{ route('stripe.checkout.premium') }}" class="plan-btn primary">
                    {{ __('messages.plan_premium_btn') }}
                </a>
            </div>

        </div>
    </section>

    <footer class="footer-pro">
        <div class="footer-bottom">
            <span>© {{ date('Y') }} aulaDesk · {{ __('messages.all_rights_reserved') }}</span>
        </div>
    </footer>

</body>
</html>