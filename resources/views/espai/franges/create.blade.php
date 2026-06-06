@push('styles')
    @vite('resources/css/espai/franges/frangesForm.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">{{ __('messages.slot_create_title') }}</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.franges.store') }}">
                    @csrf

                    <div class="field">
                        <label>{{ __('messages.order') }}</label>
                        <input type="number" name="ordre" value="{{ old('ordre') }}" min="1" required>
                        @error('ordre') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.name_optional') }}</label>
                        <input name="nom" value="{{ old('nom') }}">
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.start_time') }}</label>
                        <input type="time" name="inici" value="{{ old('inici') }}" required>
                        @error('inici') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.end_time') }}</label>
                        <input type="time" name="fi" value="{{ old('fi') }}" required>
                        @error('fi') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">{{ __('messages.save') }}</button>
                        <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>