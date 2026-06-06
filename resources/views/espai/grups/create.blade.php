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

                <h2 class="inside-title">{{ __('messages.group_create_title') }}</h2>

                <form method="POST" action="{{ route('espai.grups.store') }}">
                    @csrf

                    <div class="field">
                        <label for="nom" class="label">{{ __('messages.group_name') }}</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom') }}"
                               class="input" required autofocus>
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">{{ __('messages.group_create_title') }}</button>
                        <a href="{{ route('espai.grups.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>