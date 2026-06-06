@push('styles')
  @vite('resources/css/espai/aules/aulaForm.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.aules.store') }}">
                    @csrf
                    <h1>{{ __('messages.classroom_create_title') }}</h1>
                    <div class="field">
                        <label>{{ __('messages.name') }}</label>
                        <input name="nom" value="{{ old('nom') }}" required>
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.code') }}</label>
                        <input name="codi" value="{{ old('codi') }}">
                        @error('codi') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.capacity') }}</label>
                        <input type="number" name="capacitat" value="{{ old('capacitat') }}" min="0">
                        @error('capacitat') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.floor') }}</label>
                        <input name="planta" value="{{ old('planta') }}">
                        @error('planta') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button class="btn" type="submit">{{ __('messages.save') }}</button>
                        <a class="btn secondary" href="{{ route('espai.aules.index') }}">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>