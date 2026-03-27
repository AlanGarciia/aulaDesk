@push('styles')
    @vite('resources/css/espai/grups/grupsEdit.css')
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

                <h2 class="inside-title">Gestionar grup: {{ $grup->nom }}</h2>

                {{-- FORMULARIO GET SOLO PARA BUSCAR --}}
                <form method="GET" action="{{ route('espai.grups.edit', $grup) }}" style="margin-bottom: 12px;">
                    <div class="field">
                        <label class="label">Buscar alumne</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               class="input"
                               placeholder="Buscar alumne...">
                    </div>
                </form>

                {{-- FORMULARIO PRINCIPAL POST --}}
                <form method="POST" action="{{ route('espai.grups.update', $grup) }}">
                    @csrf
                    @method('PUT')

                    {{-- NOMBRE DEL GRUPO --}}
                    <div class="field">
                        <label for="nom" class="label">Nom del grup</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom', $grup->nom) }}"
                               class="input" required>
                    </div>

                    {{-- GRID DE ALUMNES --}}
                    <div class="field">
                        <label class="label">Alumnes del grup</label>

                        <div id="alumnes-grid" class="alumnes-grid">
                            @foreach ($alumnes as $alumne)
                                <label class="alumne-card">
                                    <input type="checkbox"
                                           name="alumnes[]"
                                           value="{{ $alumne->id }}"
                                           class="alumne-check"
                                           {{ $grup->alumnes->contains($alumne->id) ? 'checked' : '' }}>

                                    <div class="alumne-avatar">
                                        {{ strtoupper(substr($alumne->nom, 0, 1)) }}
                                    </div>

                                    <div class="alumne-info">
                                        <div class="alumne-name">
                                            {{ $alumne->nom }} {{ $alumne->cognoms }}
                                        </div>
                                        <div class="alumne-id">
                                            IDALU: {{ $alumne->idalu }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="pagination">
                            {{ $alumnes->links('vendor.pagination.three') }}
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Guardar canvis</button>
                        <a href="{{ route('espai.grups.index') }}" class="btn btn-secondary">Tornar</a>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
