@push('styles')
    @vite('resources/css/espai/usuaris/usuarisEdit.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.usuaris.update', $usuariEspai) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="nom" value="{{ old('nom', $usuariEspai->nom) }}" required>
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>{{ __('messages.role') }}</label>
                        <select name="rol" required
                            @disabled($usuariEspai->nom === 'admin' || $usuariEspai->rol === 'admin')>
                            @foreach (\App\Models\BaseRole::pluck('nom') as $rol)
                                <option value="{{ $rol }}" @selected(old('rol', $usuariEspai->rol) === $rol)>
                                    {{ ucfirst($rol) }}
                                </option>
                            @endforeach
                        </select>

                        @error('rol') <div class="error">{{ $message }}</div> @enderror

                        @if ($usuariEspai->nom === 'admin' || $usuariEspai->rol === 'admin')
                            <div class="help">{{ __('messages.admin_cannot_change_role') }}</div>
                        @endif
                    </div>

                    <div class="field">
                        <label>{{ __('messages.new_password_optional') }}</label>
                        <input type="password" name="contrasenya" autocomplete="new-password">
                        @error('contrasenya') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">{{ __('messages.save') }}</button>
                        <a class="btn btn-secondary" href="{{ route('espai.usuaris.index') }}">{{ __('messages.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>