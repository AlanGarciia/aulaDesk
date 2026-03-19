<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suport · aulaDesk</title>

    @vite(['resources/css/presentacion/suport.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>Centre de Suport</h1>
            <p>Ajuda, estat del servei i recursos per al teu centre educatiu.</p>
        </div>
    </header>

    <!-- INTRO -->
    <section class="intro">
        <p>
            Benvingut al centre de suport d’aulaDesk. Aquí trobaràs informació útil, estat del servei,
            guies ràpides i vies de contacte per resoldre qualsevol dubte.
        </p>
    </section>

    <!-- STATUS PAGE -->
    <section class="status-section">
        <h2>Estat del servei</h2>
        <p class="status-description">Consulta l’estat actual dels serveis d’aulaDesk.</p>

        <div id="service-status" class="status-box">
            <span id="status-indicator" class="status-indicator"></span>
            <span id="status-text">Carregant estat...</span>
        </div>

        <p id="status-updated" class="status-updated"></p>
    </section>

    <!-- HELP SECTIONS -->
    <section class="help-grid">

        <div class="help-card">
            <h3>Guies ràpides</h3>
            <ul>
                <li>Com importar alumnes amb CSV</li>
                <li>Com gestionar guardies</li>
                <li>Com crear grups i espais</li>
                <li>Com publicar anuncis interns</li>
            </ul>
        </div>

        <div class="help-card">
            <h3>Preguntes freqüents</h3>
            <ul>
                <li>Com puc recuperar la contrasenya?</li>
                <li>Com afegeixo un nou professor?</li>
                <li>Com funciona el sistema d’horaris?</li>
                <li>Com puc contactar amb suport?</li>
            </ul>
        </div>

        <div class="help-card">
            <h3>Contacte</h3>
            <p>Si necessites ajuda directa, pots contactar amb nosaltres:</p>
            <ul>
                <li><strong>Email:</strong> suport@auladesk.com</li>
                <li><strong>Horari:</strong> Dilluns a divendres, 9:00 - 18:00</li>
            </ul>
        </div>

    </section>

    <!-- CTA FINAL -->
    <section class="cta">
        <h2>Necessites més ajuda?</h2>
        <p>Estem aquí per ajudar-te en tot el que necessitis.</p>
        <a href="/" class="btn-secondary">Tornar a l'inici</a>
    </section>

    <!-- STATUS SCRIPT -->
    <script>
        // Cambia este valor para actualizar el estado:
        // "operatiu", "manteniment", "incidencia"
        const currentStatus = "operatiu";

        const statusIndicator = document.getElementById("status-indicator");
        const statusText = document.getElementById("status-text");
        const statusUpdated = document.getElementById("status-updated");

        function updateStatus(){
            let color = "";
            let text = "";

            switch(currentStatus){
                case "operatiu":
                    color = "#4ade80";
                    text = "🟢 Operatiu";
                    break;

                case "manteniment":
                    color = "#facc15";
                    text = "🟡 Manteniment programat";
                    break;

                case "incidencia":
                    color = "#f87171";
                    text = "🔴 Incidència detectada";
                    break;

                default:
                    color = "#9ca3af";
                    text = "Estat desconegut";
            }

            statusIndicator.style.background = color;
            statusText.textContent = text;

            const now = new Date();
            statusUpdated.textContent = "Última actualització: " + now.toLocaleString("ca-ES");
        }

        updateStatus();
    </script>

</body>
</html>
