<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>aulaDesk · Benvingut/da</title>

    @vite(['resources/css/welcome.css'])
</head>

<body>

    <!-- Nieve -->
    <div id="pixelSnow" style="width:100%; height:600px;"></div>

    <div class="bg" aria-hidden="true">
        <div class="bg__glow bg__glow--a"></div>
        <div class="bg__glow bg__glow--b"></div>
        <div class="bg__grid"></div>
    </div>

    <main class="wrap">
        <section class="card">

            <header class="header">
                <div class="logo">
                    <img src="{{ asset('img/logo_solo.png') }}" alt="Logo aulaDesk">
                </div>

                <div class="title">
                    <div class="kicker">Centre educatiu</div>
                    <h1>Benvingut/da a <span class="brand">aulaDesk</span></h1>
                </div>
            </header>

            <p class="lead">
                Gestiona el teu espai educatiu des d’un únic lloc: usuaris, espais i tot el que vindrà després.
            </p>

            <div class="actions">
                <a class="btn btn--primary" href="{{ route('register') }}">
                    <span>Registrar-se</span>
                    <span class="arrow">→</span>
                </a>

                <a class="btn" href="{{ route('login') }}">
                    <span>Iniciar sessió</span>
                    <span class="arrow">→</span>
                </a>
            </div>

            <footer class="footer">
                <span>© {{ date('Y') }} aulaDesk</span>
                <span class="status">
                    <span class="dot"></span>
                    Accés segur
                </span>
            </footer>

        </section>
    </main>

    <!-- Scripts al final para evitar errores DOM -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
    <script src="/js/pixelSnow.js"></script>
</body>
</html>