@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            @if ($errors->any())
                <div class="alert-danger">
                    {{ __('messages.check_fields_errors') }}
                </div>
            @endif

            <div class="card">

                <!-- TÍTULO DENTRO DEL CARNET -->
                <h2 class="inside-title">{{ __('messages.students_add_title') }}</h2>

                <form method="POST" action="{{ route('espai.alumnes.store') }}">
                    @csrf

                    <div class="field">
                        <label for="nom" class="label">{{ __('messages.name') }}</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom') }}"
                               class="input" autocomplete="off" autofocus required>
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="cognoms" class="label">{{ __('messages.surnames') }}</label>
                        <input id="cognoms" name="cognoms" type="text"
                               value="{{ old('cognoms') }}"
                               class="input" autocomplete="off">
                        @error('cognoms')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="correu" class="label">{{ __('messages.email') }}</label>
                        <input id="correu" name="correu" type="email"
                               value="{{ old('correu') }}"
                               class="input" autocomplete="off">
                        @error('correu')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="idalu" class="label">{{ __('messages.idalu_label') }}</label>
                        <input id="idalu" name="idalu" type="text"
                               maxlength="11"
                               value="{{ old('idalu') }}"
                               class="input" autocomplete="off" required>
                        @error('idalu')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="telefon" class="label">{{ __('messages.phone') }}</label>
                        <input id="telefon" name="telefon" type="text"
                               value="{{ old('telefon') }}"
                               class="input" autocomplete="off">
                        @error('telefon')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            {{ __('messages.students_create_btn') }}
                        </button>

                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

</x-app-layout>