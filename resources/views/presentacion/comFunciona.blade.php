<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Com funciona · aulaDesk</title>

    @vite(['resources/css/presentacion/com-funciona.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>Com funciona aulaDesk</h1>
            <p>Una plataforma pensada per simplificar la gestió del teu centre educatiu.</p>
        </div>
    </header>

    <!-- INTRO -->
    <section class="intro">
        <p>
            aulaDesk està dissenyat perquè qualsevol centre educatiu pugui gestionar usuaris, espais, grups i processos 
            administratius de manera ràpida i intuïtiva.  
            A continuació t’expliquem com funciona, pas a pas.
        </p>
    </section>

    <!-- PASO 1 -->
    <section class="step">
        <div class="step-content">
            <div class="text">
                <h2>1. Registra el teu centre</h2>
                <p>
                    En pocs minuts pots crear el teu centre i configurar les dades bàsiques: nom, tipus d’institució, 
                    horaris i preferències generals.  
                    Tot queda preparat perquè puguis començar a treballar immediatament.
                </p>
            </div>
            <div class="icon">
                <img class="big-img" src="{{ Vite::asset('resources/images/crear_espais.png') }}" alt="Centre">
            </div>
        </div>
    </section>

    <!-- PASO 2 -->
    <section class="step alt">
        <div class="step-content">
            <div class="icon">
                <img class="big-img" src="{{ Vite::asset('resources/images/afegir_usuaris.png') }}" alt="Usuaris">
            </div>
            <div class="text">
                <h2>2. Afegeix usuaris i assigna rols</h2>
                <p>
                    Importa alumnes, professors i personal administratiu.  
                    Assigna rols i permisos perquè cada persona tingui accés només al que necessita.
                </p>
            </div>
        </div>
    </section>

    <!-- PASO 3 -->
    <section class="step">
        <div class="step-content">
            <div class="text">
                <h2>3. Organitza espais i aules</h2>
                <p>
                    Crea aules, laboratoris, sales especials i qualsevol espai del centre.  
                    Controla disponibilitat, capacitat i assignacions de manera visual i ordenada.
                </p>
            </div>
            <div class="icon">
                <img class="big-img" src="{{ Vite::asset('resources/images/aules.png') }}" alt="Espais">
            </div>
        </div>
    </section>

    <!-- PASO 4 -->
    <section class="step alt">
        <div class="step-content">
            <div class="icon">
                <img class="big-img" src="{{ Vite::asset('resources/images/guardies.png') }}" alt="Grups">
            </div>
            <div class="text">
                <h2>4. Creació de guardies</h2>
                <p>
                    No hi ha professor per aquella hora?  
                    Assigna professors responsables i gestiona l’assistència de manera centralitzada.
                </p>
            </div>
        </div>
    </section>

    <!-- PASO 5 -->
    <section class="step">
        <div class="step-content">
            <div class="text">
                <h2>5. Tauló de noticies</h2>
                <p>
                    Consulta el tauló de noticies amb tota la informació del centre: activitat, guardies, 
                    vagues estudiantils... i més! 
                    Tot el que necessites, en un sol lloc.
                </p>
            </div>
            <div class="icon">
                <img class="big-img" src="{{ Vite::asset('resources/images/noticias.png') }}" alt="Dashboard">
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta">
        <h2>Comença avui mateix</h2>
        <p>Prova aulaDesk gratuïtament i descobreix com pot ajudar al teu centre.</p>
        <a href="{{ route('register') }}" class="btn-primary">Crear compte</a>
    </section>

</body>
</html>
