@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            @if ($errors->any())
                <div class="alert-danger">
                    Revisa els camps, hi ha errors.
                </div>
            @endif

            <div class="card">

                <!-- TÍTULO DENTRO DEL CARNET -->
                <h2 class="inside-title">Afegir alumne</h2>

                <form method="POST" action="{{ route('espai.alumnes.store') }}">
                    @csrf

                    <div class="field">
                        <label for="nom" class="label">Nom</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom') }}"
                               class="input" autocomplete="off" autofocus required>
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="cognoms" class="label">Cognoms</label>
                        <input id="cognoms" name="cognoms" type="text"
                               value="{{ old('cognoms') }}"
                               class="input" autocomplete="off">
                        @error('cognoms')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="correu" class="label">Correu</label>
                        <input id="correu" name="correu" type="email"
                               value="{{ old('correu') }}"
                               class="input" autocomplete="off">
                        @error('correu')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="idalu" class="label">IDALU (11 dígits)</label>
                        <input id="idalu" name="idalu" type="text"
                               maxlength="11"
                               value="{{ old('idalu') }}"
                               class="input" autocomplete="off" required>
                        @error('idalu')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="telefon" class="label">Telèfon</label>
                        <input id="telefon" name="telefon" type="text"
                               value="{{ old('telefon') }}"
                               class="input" autocomplete="off">
                        @error('telefon')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            Crear alumne
                        </button>

                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">
                            Cancel·lar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

</x-app-layout>
