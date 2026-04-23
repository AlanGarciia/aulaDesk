<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>aulaDesk · Plans</title>

    @vite(['resources/css/presentacion/welcome.css'])
</head>

<body>

   <div class="logo">
        <img src="{{ asset('img/logo_solo.png') }}" alt="Logo aulaDesk">
    </div>

    <!-- ============================
        SECCIÓN — PLANS
    ============================= -->
    <section class="section pricing">
        <div class="pricing-grid">

            <!-- PLAN GRATUIT -->
            <div class="plan-card">
                <h3 class="plan-title">Gratuït</h3>
                <p class="plan-subtitle">Perfecte per començar</p>

                <ul class="plan-features">
                    <li>Sense compromís</li>
                    <li>Ideal per centres petits</li>
                    <li>Accés immediat</li>
                </ul>

                <a href="{{ route('register') }}" class="plan-btn">
                    Començar gratis
                </a>
            </div>

            <!-- PLAN PREMIUM DESTACADO -->
            <div class="plan-card plan-featured">
                <div class="badge">Més popular</div>

                <h3 class="plan-title">Premium</h3>
                <p class="plan-subtitle">La millor experiència</p>

                <ul class="plan-features">
                    <li>Rendiment superior</li>
                    <li>Funcions avançades</li>
                    <li>Millor suport</li>
                </ul>

                <a href="{{ route('stripe.checkout.premium') }}" class="plan-btn primary">
                    Començar Premium
                </a>
            </div>

        </div>
    </section>

    <footer class="footer-pro">
        <div class="footer-bottom">
            <span>© {{ date('Y') }} aulaDesk · Tots els drets reservats</span>
        </div>
    </footer>

</body>
</html>