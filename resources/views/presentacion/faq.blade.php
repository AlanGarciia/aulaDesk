<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntes freqüents · aulaDesk</title>

    @vite(['resources/css/presentacion/faq.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>Preguntes freqüents</h1>
            <p>Tot el que necessites saber sobre aulaDesk, explicat de manera clara i directa.</p>
        </div>
    </header>

    <!-- INTRO -->
    <section class="intro">
        <p>
            Hem recopilat les preguntes més habituals que ens fan els centres educatius.  
            Si tens algun dubte que no apareix aquí, pots contactar amb nosaltres en qualsevol moment.
        </p>
    </section>

    <!-- FAQ -->
    <section class="faq-section">

        <div class="faq-item">
            <button class="faq-question">Què és aulaDesk?</button>
            <div class="faq-answer">
                <p>
                    aulaDesk és una plataforma de gestió per a centres educatius que permet administrar usuaris, espais, 
                    grups, horaris, guardies i molt més des d’un únic lloc.
                </p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">És gratuït començar a utilitzar aulaDesk?</button>
            <div class="faq-answer">
                <p>
                    Sí. Disposem d’un pla gratuït amb totes les funcions bàsiques perquè puguis començar sense cap cost.  
                    Pots actualitzar al pla Premium en qualsevol moment.
                </p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Quines diferències hi ha entre el pla Gratuït i el Premium?</button>
            <div class="faq-answer">
                <p>
                    El pla Premium inclou usuaris il·limitats, gestió avançada d’espais, integracions externes, 
                    permisos avançats, informes detallats i suport prioritari.
                </p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Puc cancel·lar el meu pla en qualsevol moment?</button>
            <div class="faq-answer">
                <p>
                    Per descomptat. Pots cancel·lar o modificar el teu pla quan vulguis, sense permanència ni penalitzacions.
                </p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">És segur utilitzar aulaDesk?</button>
            <div class="faq-answer">
                <p>
                    Sí. Totes les dades estan protegides amb encriptació i seguim estrictes protocols de seguretat 
                    per garantir la privacitat del teu centre.
                </p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Quin tipus de suport oferiu?</button>
            <div class="faq-answer">
                <p>
                    Oferim suport per email en el pla gratuït i suport prioritari en el pla Premium.  
                    Els centres Enterprise disposen d’assistència 24/7.
                </p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Puc importar dades del meu sistema actual?</button>
            <div class="faq-answer">
                <p>
                    Sí. Podem ajudar-te a importar usuaris, grups i espais des d’altres plataformes o arxius CSV.
                </p>
            </div>
        </div>

    </section>

    <!-- CTA FINAL -->
    <section class="cta">
        <h2>Encara tens dubtes?</h2>
        <p>Contacta amb nosaltres i t’ajudarem encantats.</p>
        <a href="{{ route('register') }}" class="btn-primary">Començar ara</a>
    </section>

    <section class="contact-form">
    <h2>Envia’ns la teva consulta</h2>
    <p>Responem normalment en menys de 24 hores.</p>

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
            <label for="message">Missatge</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn-primary">Enviar missatge</button>

        <p id="fakeSuccess" class="success" style="display:none;">
            Missatge enviat correctament! Ens posarem en contacte amb tu aviat.
        </p>
    </form>
</section>

    <script>
        // Animación FAQ
        document.querySelectorAll('.faq-question').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.classList.toggle('active');
                const answer = btn.nextElementSibling;
                answer.style.maxHeight = answer.style.maxHeight ? null : answer.scrollHeight + "px";
            });
        });
    </script>

</body>
</html>
