@push('styles')
    @vite('resources/css/espais/entrarEspai.css')
@endpush

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Entrar a l'espai</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="page">
        <div class="container">
            <div class="card">
                <h1 class="title">Entrar a l'espai</h1>
                <p class="subtitle">Introdueix el teu usuari d'espai per continuar.</p>

                <form method="POST" action="{{ route('espais.entrar', $espai) }}">
                    @csrf

                    <div class="field">
                        <label for="nom" class="label">Nom</label>
                        <input id="nom" name="nom" class="input" type="text"
                               value="{{ old('nom') }}" required autofocus>
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="contrasenya" class="label">Contrasenya</label>
                        <input id="contrasenya" name="contrasenya" class="input" type="password" required>
                        @error('contrasenya')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                        <a href="{{ route('espais.index') }}" class="btn btn-secondary">Tornar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
