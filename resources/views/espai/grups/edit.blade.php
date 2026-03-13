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

                <form method="POST" action="{{ route('espai.grups.update', $grup) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="nom" class="label">Nom del grup</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom', $grup->nom) }}"
                               class="input" required>
                    </div>

                    <div class="field">
                        <label class="label">Alumnes del grup</label>

                        <input type="text" id="search" class="input" placeholder="Buscar alumne...">

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

                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Guardar canvis</button>
                        <a href="{{ route('espai.grups.index') }}" class="btn btn-secondary">Tornar</a>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const grid = document.getElementById('alumnes-grid');

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            const cards = grid.querySelectorAll('.alumne-card');

            cards.forEach(card => {
                const name = card.querySelector('.alumne-name').textContent.toLowerCase();
                card.style.display = name.includes(term) ? '' : 'none';
            });
        });
    </script>

</x-app-layout>