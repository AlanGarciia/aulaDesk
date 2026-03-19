<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre nosaltres · aulaDesk</title>

    @vite(['resources/css/presentacion/sobre.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>Sobre nosaltres</h1>
            <p>La plataforma creada per facilitar el dia a dia dels centres educatius.</p>
        </div>
    </header>

    <!-- INTRO -->
    <section class="intro">
        <p>
            A aulaDesk treballem amb un objectiu clar: simplificar la gestió dels instituts i ajudar els professors 
            a centrar-se en allò que realment importa, l’educació. Som un equip apassionat per la tecnologia i 
            compromès amb la millora contínua.
        </p>
    </section>

    <!-- MISIÓN -->
    <section class="section">
        <h2>La nostra missió</h2>
        <p>
            Oferir una eina moderna, intuïtiva i segura que permeti als centres educatius organitzar-se millor, 
            reduir tasques repetitives i millorar la comunicació interna.
        </p>
    </section>

    <!-- QUÉ HACEMOS -->
    <section class="section">
        <h2>Què fem</h2>
        <ul class="list">
            <li>Gestió d’alumnes amb importació i exportació CSV.</li>
            <li>Creació i publicació d’anuncis interns.</li>
            <li>Organització de guardies de professors.</li>
            <li>Administració d’horaris, grups i espais.</li>
            <li>Funcions avançades per a la coordinació del centre.</li>
        </ul>
    </section>

    <!-- VISIÓN -->
    <section class="section">
        <h2>La nostra visió</h2>
        <p>
            Convertir-nos en la plataforma de referència per a la gestió educativa, evolucionant constantment 
            segons les necessitats reals dels centres i del professorat.
        </p>
    </section>

    <!-- COMPROMISO -->
    <section class="section">
        <h2>El nostre compromís</h2>
        <ul class="list">
            <li>Innovació contínua.</li>
            <li>Simplicitat en cada funció.</li>
            <li>Seguretat i privacitat de les dades.</li>
            <li>Suport proper i humà.</li>
        </ul>
    </section>

    <!-- CTA FINAL -->
    <section class="cta">
        <h2>Vols saber més?</h2>
        <p>Descobreix totes les funcions d’aulaDesk i com pot ajudar al teu centre.</p>
        <a href="{{ route('register') }}" class="btn-primary">Començar ara</a>

        <!-- BOTÓN DE INICIO -->
        <a href="/" class="btn-secondary">Tornar a l'inici</a>
    </section>

</body>
</html>
