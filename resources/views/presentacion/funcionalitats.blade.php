<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionalitats · aulaDesk</title>

    @vite(['resources/css/presentacion/funcionalitats.css'])
</head>

<body>

    <!-- Hero -->
    <header class="hero">
        <div class="hero-content">
            <h1>Funcionalitats d’aulaDesk</h1>
            <p>Tot el que necessites per gestionar el teu centre educatiu des d’un únic lloc.</p>
        </div>
    </header>

    <!-- Sección 1 -->
    <section class="feature-section">
        <div class="feature">
            <div class="text">
                <h2>Gestió d’usuaris i rols</h2>
                <p>
                    Administra fàcilment professors, alumnes, personal administratiu i convidats. 
                    Assigna permisos personalitzats i controla l’accés a cada espai.
                </p>
            </div>
            <div class="image">
                <img src="{{ Vite::asset('resources/images/gestio.png') }}" alt="Dashboard aulaDesk">
        </div>

            </div>
        </div>
    </section>

    <!-- Sección 2 -->
    <section class="feature-section alt">
    <div class="feature">

        <div class="image">
            <img src="{{ Vite::asset('resources/images/dashboard.png') }}" alt="Dashboard aulaDesk">
        </div>

        <div class="text">
            <h2>Administració d’espais i aules</h2>
            <p>
                Organitza totes les aules, laboratoris, sales i espais del centre. 
                Controla disponibilitat, capacitat i assignacions.
            </p>
        </div>

    </div>
</section>


    <!-- Sección 3 -->
    <section class="feature-section">
        <div class="feature">
            <div class="text">
                <h2>Panell d’administració intuïtiu</h2>
                <p>
                    Una interfície moderna i clara que et permet veure tota la informació del centre 
                    d’un sol cop d’ull.
                </p>
            </div>
            <div class="image">
                <img src="{{ Vite::asset('resources/images/configuracio.png') }}" alt="Dashboard aulaDesk">
        </div>

            </div>
        </div>
    </section>


    <!-- CTA -->
    <section class="cta">
        <h2>Vols provar aulaDesk?</h2>
        <p>Comença gratuïtament i descobreix totes les funcionalitats.</p>
        <a href="{{ route('register') }}" class="btn-primary">Crear compte</a>
    </section>

</body>
</html>
