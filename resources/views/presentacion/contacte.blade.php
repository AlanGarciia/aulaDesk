<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacte · aulaDesk</title>

    @vite(['resources/css/presentacion/contacte.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>Contacte</h1>
            <p>Som aquí per ajudar-te en tot el que necessitis.</p>
        </div>
    </header>

    <!-- INTRO -->
    <section class="intro">
        <p>
            Si tens dubtes, suggeriments o vols més informació sobre aulaDesk, pots contactar amb nosaltres 
            a través del formulari següent. Responem habitualment en menys de 24 hores.
        </p>
    </section>

    <!-- CONTACT WRAPPER -->
    <section class="contact-wrapper">

        <!-- FORM COLUMN -->
        <div class="contact-form">
            <h2>Envia’ns un missatge</h2>
            <p>Et respondrem tan aviat com sigui possible.</p>

            <form id="fakeForm">
                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Correu electrònic</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="subject">Assumpte</label>
                    <input type="text" id="subject" name="subject" required>
                </div>

                <div class="form-group">
                    <label for="message">Missatge</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <!-- BOTONES JUNTOS -->
                <div class="button-row">
                    <button type="submit" class="btn-primary">Enviar missatge</button>
                    <a href="/" class="btn-secondary">Tornar a l'inici</a>
                </div>

                <p id="fakeSuccess" class="success" style="display:none;">
                    Missatge enviat correctament! Ens posarem en contacte amb tu aviat.
                </p>
            </form>
        </div>

        <!-- INFO COLUMN -->
        <div class="contact-info">
            <h2>Altres vies de contacte</h2>

            <ul>
                <li><strong>Email:</strong> suport@auladesk.com</li>
                <li><strong>Horari d’atenció:</strong> Dilluns a divendres, 9:00 - 18:00</li>
                <li><strong>Temps de resposta:</strong> Normalment menys de 24 hores</li>
            </ul>

            <h3>Per què contactar amb nosaltres?</h3>
            <p>
                Tant si tens preguntes sobre el funcionament d’aulaDesk, vols suggerir noves funcionalitats 
                o necessites assistència, estarem encantats d’ajudar-te.
            </p>
        </div>

    </section>

    <script>
        // Fake form success
        document.getElementById('fakeForm').addEventListener('submit', function(e){
            e.preventDefault();
            document.getElementById('fakeSuccess').style.display = 'block';
        });
    </script>

</body>
</html>
