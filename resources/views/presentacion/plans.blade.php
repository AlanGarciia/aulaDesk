<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans i Preus · aulaDesk</title>

    @vite(['resources/css/presentacion/plans.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>Plans i Preus</h1>
            <p>Tria el pla que millor s’adapta al teu centre educatiu i creix al teu ritme.</p>
        </div>
    </header>

    <!-- INTRO -->
    <section class="intro">
        <p>
            aulaDesk ofereix diferents plans pensats per a centres educatius de totes les mides. 
            Tant si ets una acadèmia petita com un centre gran amb centenars d’usuaris, tenim una solució per a tu.
        </p>
    </section>

    <!-- PRICING TABLE -->
    <section class="pricing-section">

        <div class="pricing-grid">

            <!-- FREE -->
            <div class="plan">
                <h3>Gratuït</h3>
                <p class="subtitle">Ideal per a centres petits o projectes inicials</p>

                <div class="price">0€<span>/mes</span></div>

                <ul class="features">
                    <li><strong>Fins a 50 usuaris</strong> per començar sense límits econòmics.</li>
                    <li><strong>Gestió bàsica d’espais</strong> per organitzar aules i sales.</li>
                    <li><strong>Panell d’administració simple</strong> amb les funcions essencials.</li>
                    <li><strong>Control d’assistència manual</strong> per grups reduïts.</li>
                    <li><strong>Suport per email</strong> amb resposta en 48-72h.</li>
                </ul>

                <a href="{{ route('register') }}" class="btn-primary">Començar</a>
            </div>

            <!-- PREMIUM -->
            <div class="plan featured">
                <div class="badge">Més popular</div>

                <h3>Premium</h3>
                <p class="subtitle">Per a centres que necessiten més control i automatització</p>

                <div class="price">19€<span>/mes</span></div>

                <ul class="features">
                    <li><strong>Usuaris il·limitats</strong> sense cap cost addicional.</li>
                    <li><strong>Gestió avançada d’espais</strong> amb calendaris i disponibilitat.</li>
                    <li><strong>Integracions externes</strong> (Google Workspace, Microsoft 365...).</li>
                    <li><strong>Control d’assistència automàtic</strong> amb registres i informes.</li>
                    <li><strong>Panell d’administració complet</strong> amb estadístiques i informes.</li>
                    <li><strong>Permisos avançats</strong> per rols i grups.</li>
                    <li><strong>Suport prioritari</strong> amb resposta en menys de 24h.</li>
                </ul>

                <a href="{{ route('register') }}" class="btn-primary">Provar Premium</a>
            </div>

            <!-- ENTERPRISE -->
            <div class="plan">
                <h3>Enterprise</h3>
                <p class="subtitle">Solucions a mida per a centres grans</p>

                <div class="price">Contactar</div>

                <ul class="features">
                    <li><strong>Infraestructura dedicada</strong> amb servidors exclusius.</li>
                    <li><strong>Funcions personalitzades</strong> segons les necessitats del centre.</li>
                    <li><strong>Integració amb sistemes interns</strong> (ERP, CRMs, control d’accés...).</li>
                    <li><strong>Formació i onboarding</strong> per a tot el personal.</li>
                    <li><strong>Suport 24/7</strong> amb assistència directa.</li>
                    <li><strong>Consultoria tècnica</strong> per optimitzar processos.</li>
                </ul>

                <a href="#" class="btn-primary">Contactar</a>
            </div>

        </div>

    </section>

    <!-- COMPARATIVA -->
    <section class="comparison">
        <h2>Comparativa de funcionalitats</h2>

        <div class="comparison-table">
            <div class="row header">
                <span>Funcionalitat</span>
                <span>Gratuït</span>
                <span>Premium</span>
                <span>Enterprise</span>
            </div>

            <div class="row">
                <span>Usuaris il·limitats</span>
                <span>—</span>
                <span>✔</span>
                <span>✔</span>
            </div>

            <div class="row">
                <span>Gestió d’espais avançada</span>
                <span>—</span>
                <span>✔</span>
                <span>✔</span>
            </div>

            <div class="row">
                <span>Integracions externes</span>
                <span>—</span>
                <span>✔</span>
                <span>✔</span>
            </div>

            <div class="row">
                <span>Suport prioritari</span>
                <span>—</span>
                <span>✔</span>
                <span>✔</span>
            </div>

            <div class="row">
                <span>Funcions personalitzades</span>
                <span>—</span>
                <span>—</span>
                <span>✔</span>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta">
        <h2>Comença avui mateix</h2>
        <p>Prova aulaDesk gratuïtament i descobreix totes les seves funcionalitats.</p>
        <a href="{{ route('register') }}" class="btn-primary">Crear compte</a>
    </section>

</body>
</html>
