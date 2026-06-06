@push('styles')
    @vite('resources/css/espais/espaisCreate.css')
@endpush

@push('scripts')
    @vite('resources/js/espais/particles-bg.js')
@endpush

<x-app-layout>
    <div class="create-page particles-page">
        {{-- Fondo de partículas --}}
        <div id="particles-bg" class="particles-bg" aria-hidden="true"></div>

        <div class="create-postit create-foreground">
            <h2 class="create-title">{{ __('messages.espais_create_title') }}</h2>

            <h3 class="create-subtitle">
                {{ __('messages.espais_create_subtitle') }}
            </h3>

            <form method="POST" action="{{ route('espais.store') }}">
                @csrf

                <div class="form-group">
                    <label for="nom">{{ __('messages.name') }}</label>
                    <input id="nom" name="nom" type="text"
                           value="{{ old('nom') }}"
                           placeholder="{{ __('messages.espais_name_placeholder') }}" autofocus>
                    @error('nom')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descripcio">{{ __('messages.description_optional') }}</label>
                    <textarea id="descripcio" name="descripcio" rows="4"
                              placeholder="{{ __('messages.description_placeholder') }}">{{ old('descripcio') }}</textarea>
                    @error('descripcio')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('espais.index') }}" class="btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>