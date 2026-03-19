@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <div class="card">

                <h2 class="inside-title">Editar alumne</h2>

                @if ($errors->any())
                    <div class="alert-danger">
                        Hi ha errors en el formulari. Revisa els camps.
                    </div>
                @endif

                <form method="POST" action="{{ route('espai.alumnes.update', $alumne) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="nom" class="label">Nom</label>
                        <input id="nom"
                               name="nom"
                               type="text"
                               class="input"
                               value="{{ old('nom', $alumne->nom) }}"
                               required>
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="cognoms" class="label">Cognoms</label>
                        <input id="cognoms"
                               name="cognoms"
                               type="text"
                               class="input"
                               value="{{ old('cognoms', $alumne->cognoms) }}">
                        @error('cognoms')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="correu" class="label">Correu</label>
                        <input id="correu"
                               name="correu"
                               type="email"
                               class="input"
                               value="{{ old('correu', $alumne->correu) }}">
                        @error('correu')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="idalu" class="label">IDALU</label>
                        <input id="idalu"
                               name="idalu"
                               type="text"
                               class="input"
                               value="{{ old('idalu', $alumne->idalu) }}"
                               required>
                        @error('idalu')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="telefon" class="label">Telèfon</label>
                        <input id="telefon"
                               name="telefon"
                               type="text"
                               class="input"
                               value="{{ old('telefon', $alumne->telefon) }}">
                        @error('telefon')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Guardar canvis</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">Cancel·lar</a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
