<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>aulaDesk · Benvingut/da</title>

    @vite(['resources/css/presentacion/welcome.css'])
</head>

<body>

    <!-- Nieve -->
    <div id="pixelSnow"></div>

    <div class="bg" aria-hidden="true">
        <div class="bg__glow bg__glow--a"></div>
        <div class="bg__glow bg__glow--b"></div>
        <div class="bg__grid"></div>
    </div>

    <!-- ============================
        HERO PRINCIPAL
    ============================= -->
    <main class="wrap">
        <section class="card">

            <header class="header">
                <div class="logo">
                    <img src="{{ asset('img/logo_solo.png') }}" alt="Logo aulaDesk">
                </div>

                <div class="title">
                    <div class="kicker">Gestió educativa reinventada</div>
                    <h1>Benvingut/da a <span class="brand">aulaDesk</span></h1>
                </div>
            </header>

            <p class="lead">
                Organitza, coordina i impulsa el teu centre educatiu amb una plataforma  
                creada per fer-te la vida més fàcil.
            </p>

            <div class="actions">
                <a class="btn btn--primary" href="{{ route('register') }}">
                    <span>Començar ara</span>
                    <span class="arrow">→</span>
                </a>

                <a class="btn" href="{{ route('login') }}">
                    <span>Ja tinc compte</span>
                    <span class="arrow">→</span>
                </a>
            </div>

            <footer class="footer">
                <span>© {{ date('Y') }} aulaDesk</span>
                <span class="status">
                    <span class="dot"></span>
                    Connexió segura
                </span>
            </footer>

        </section>
    </main>

    <!-- ============================
        SECCIÓN 1 — TAGLINE VISUAL
    ============================= -->
    <section class="section about">
        <h2>Una plataforma pensada per a centres que volen avançar</h2>
        <p>
            No és només tecnologia. És una nova manera de treballar: més clara, més humana i molt més eficient.
        </p>
    </section>

    <!-- ============================
        SECCIÓN 2 — BLOQUES VISUALES
    ============================= -->
    <section class="section features">
        <h2>El que fa diferent aulaDesk</h2>

        <ul class="feature-cards">

            <li class="card-item">
                <h3>Simplicitat real</h3>
                <p>Tot està dissenyat perquè puguis fer més amb menys. Menys clics, menys caos, més agilitat.</p>
            </li>

            <li class="card-item">
                <h3>Velocitat i fluïdesa</h3>
                <p>Res de pantalles lentes o processos eterns. Tot respon al moment.</p>
            </li>

            <li class="card-item">
                <h3>Enfocat en el que importa</h3>
                <p>El centre funciona millor quan les eines no molesten. aulaDesk desapareix i tu treballes.</p>
            </li>

            <li class="card-item">
                <h3>Confiança i seguretat</h3>
                <p>Dades protegides, accés segur i tranquil·litat per a tot el personal.</p>
            </li>
            
            <li class="card-item">
                <h3>Evolució constant</h3>
                <p>Actualitzacions freqüents, millores contínues i noves funcionalitats sense interrompre el teu dia a dia.</p>
            </li>

            <li class="card-item">
                <h3>Pensat per a equips</h3>
                <p>Tot el centre treballa alineat: direcció, professorat i administració comparteixen un mateix espai fluid.</p>
            </li>
        </ul>
    </section>

    <!-- ============================
        SECCIÓN 3 — EXPERIENCIA
    ============================= -->
    <section class="section screenshots">
        <h2>Una experiència que inspira</h2>

        <div class="gallery">

            <div class="screenshot">
                <h3>Interfície moderna</h3>
                <p>Tot és net, ordenat i agradable. Treballar-hi és un gust.</p>
            </div>

            <div class="screenshot">
                <h3>Fluxos naturals</h3>
                <p>Les tasques del dia a dia flueixen de manera intuïtiva i sense friccions.</p>
            </div>

            <div class="screenshot">
                <h3>Actualitzacions constants</h3>
                <p>La plataforma evoluciona amb tu. Sempre millora, mai s’atura.</p>
            </div>

        </div>
    </section>

    <!-- ============================
        SECCIÓN 4 — PLANS
    ============================= -->
    <section class="section pricing">
    <h2>Plans per a cada centre</h2>
    <p>Comença sense risc i evoluciona quan ho necessitis.</p>

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

            <a href="#" class="plan-btn">Començar</a>
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

        </div>

    </div>
</section>


    

    <!-- ============================
        FOOTER
    ============================= -->
    <footer class="footer-pro">
        <div class="footer-container">

            <div class="footer-col">
                <h3>aulaDesk</h3>
                <p>La plataforma moderna per gestionar centres educatius de manera simple i segura.</p>
            </div>

            <div class="footer-col">
                <h4>Producte</h4>
                <ul>
                    <li><a href="funcionalitats">Funcionalitats</a></li>
                    <li><a href="plans">Plans i preus</a></li>
                    <li><a href="comFunciona">Per què AulaDesk?</a></li>
                    <li><a href="faq">Preguntes freqüents</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Empresa</h4>
                <ul>
                    <li><a href="sobreNosotros">Sobre nosaltres</a></li>
                    <li><a href="contacte">Contacte</a></li>
                    <li><a href="blog">Blog</a></li>
                    <li><a href="suport">Suport</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Codi de l'aplicació</h4>
                <div class="social-links">
                    <a href="https://github.com/AlanGarciia/aulaDesk" target="_blank">
                        <img src="{{ Vite::asset('resources/images/github.svg') }}" alt="GitHub">
                        GitHub
                    </a>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <span>© {{ date('Y') }} aulaDesk · Tots els drets reservats</span>
            <span><a href="#">Política de privacitat</a> · <a href="#">Termes d’ús</a></span>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
    <script src="/js/pixelSnow.js"></script>
</body>
</html>
