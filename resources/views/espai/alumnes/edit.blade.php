@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <div class="card">

                <h2 class="inside-title">{{ __('messages.students_edit_title') }}</h2>

                @if ($errors->any())
                    <div class="alert-danger">
                        {{ __('messages.form_has_errors') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('espai.alumnes.update', $alumne) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="nom" class="label">{{ __('messages.name') }}</label>
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
                        <label for="cognoms" class="label">{{ __('messages.surnames') }}</label>
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
                        <label for="correu" class="label">{{ __('messages.email') }}</label>
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
                        <label for="idalu" class="label">{{ __('messages.idalu') }}</label>
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
                        <label for="telefon" class="label">{{ __('messages.phone') }}</label>
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
                        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>