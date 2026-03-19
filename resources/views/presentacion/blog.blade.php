<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog · aulaDesk</title>

    @vite(['resources/css/presentacion/blog.css'])
</head>

<body>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-content">
            <h1>El Blog d’aulaDesk</h1>
            <p>Idees, novetats i recursos per transformar la gestió educativa.</p>
        </div>
    </header>

    <!-- SEARCH + CATEGORIES -->
    <section class="toolbar">
        <input type="text" placeholder="Cerca articles..." class="search-input">

        <div class="categories">
            <button class="chip active">Tots</button>
            <button class="chip">Novetats</button>
            <button class="chip">Gestió educativa</button>
            <button class="chip">Tecnologia</button>
            <button class="chip">Consells</button>
        </div>
    </section>

    <!-- FEATURED ARTICLE -->
    <section class="featured">
        <div class="featured-image">
            <img src="{{ Vite::asset('resources/images/article1.jpg') }}" alt="Article destacat">
        </div>

        <div class="featured-text">
            <span class="tag">Destacat</span>
            <h2>Com digitalitzar el teu centre educatiu de manera eficient</h2>
            <p>
                Una guia pràctica per començar la transformació digital del teu centre sense complicacions.
                Estratègies, eines i bones pràctiques per fer el salt amb seguretat.
            </p>
        </div>
    </section>

    <!-- GRID DE ARTICLES -->
    <section class="blog-grid">

        <article class="card">
            <img src="{{ Vite::asset('resources/images/article2.avif') }}" alt="">
            <div class="card-body">
                <h3>5 eines digitals imprescindibles per a professors</h3>
                <p>Millora la productivitat i la comunicació dins del centre amb aquestes eines clau.</p>
            </div>
        </article>

        <article class="card tall">
            <img src="{{ Vite::asset('resources/images/article3.jpg') }}" alt="">
            <div class="card-body">
                <h3>Novetats d’aulaDesk: funcionalitats que t’encantaran</h3>
                <p>Un resum de les últimes millores i eines que hem afegit a la plataforma.</p>
            </div>
        </article>

        <article class="card">
            <img src="{{ Vite::asset('resources/images/article4.png') }}" alt="">
            <div class="card-body">
                <h3>Com gestionar guardies de manera eficient</h3>
                <p>Estratègies per reduir el caos i millorar la coordinació interna.</p>
            </div>
        </article>

        <article class="card wide">
            <img src="{{ Vite::asset('resources/images/article5.jpg') }}" alt="">
            <div class="card-body">
                <h3>Errors comuns en la gestió escolar i com evitar-los</h3>
                <p>Una llista de problemes habituals i com solucionar-los amb eines modernes.</p>
            </div>
        </article>

    </section>

    <!-- CTA FINAL -->
    <section class="cta">
        <h2>Vols estar al dia?</h2>
        <p>Subscriu-te per rebre novetats i actualitzacions d’aulaDesk.</p>
        <a href="{{ route('register') }}" class="btn-primary">Crear compte</a>
        <a href="/" class="btn-secondary">Tornar a l'inici</a>
    </section>

</body>
</html>
