@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Alumnes de l'espai</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.alumnes.create') }}" class="btn btn-primary">
                + Afegir alumne
            </a>

            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                <i class="bi bi-box-arrow-right"></i>
                Sortir
            </a>
        </div>

        <div class="container">

            @if (session('ok'))
                <div class="alert-success">
                    {{ session('ok') }}
                </div>
            @endif

            {{-- FILTRES --}}
            <form method="GET" action="{{ route('espai.alumnes.index') }}" class="filters-form">
                <div class="filters-grid">

                    <div class="filter-group">
                        <label for="nom">Nom</label>
                        <input type="text"
                               name="nom"
                               id="nom"
                               value="{{ request('nom') }}"
                               placeholder="Buscar per nom">
                    </div>

                    <div class="filter-group">
                        <label for="idalu">IDALU</label>
                        <input type="text"
                               name="idalu"
                               id="idalu"
                               value="{{ request('idalu') }}"
                               placeholder="Buscar per IDALU">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">Netejar</a>
                    </div>

                </div>
            </form>

            {{-- LLISTAT --}}
            <div class="card">

                @forelse ($alumnes as $alumne)
                    <div class="user-row">

                        <div class="user-info">
                            <div class="user-name">
                                {{ $alumne->nom }} {{ $alumne->cognoms }}
                            </div>

                            <div class="user-meta">
                                IDALU: {{ $alumne->idalu }}<br>
                                @if($alumne->correu)
                                    Correu: {{ $alumne->correu }}<br>
                                @endif
                                @if($alumne->telefon)
                                    Telèfon: {{ $alumne->telefon }}
                                @endif
                            </div>
                        </div>

                        <div class="user-actions">

                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.alumnes.destroy', $alumne) }}"
                                  onsubmit="return confirm('Segur que vols eliminar aquest alumne?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    Eliminar
                                </button>
                            </form>

                        </div>

                    </div>
                @empty
                    <p class="empty-state">No hi ha alumnes creats en aquest espai.</p>
                @endforelse

            </div>

            <div class="pagination">
                {{ $alumnes->links() }}
            </div>

        </div>
    </div>
</x-app-layout>