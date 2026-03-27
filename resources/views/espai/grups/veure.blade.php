@push('styles')
    @vite('resources/css/espai/grups/grupsEdit.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <div class="card">

                <h2 class="inside-title">Alumnes del grup: {{ $grup->nom }}</h2>

                <div class="field">
                    <label class="label">Nom del grup</label>
                    <div class="input" style="background: rgba(255,255,255,0.15);">
                        {{ $grup->nom }}
                    </div>
                </div>

                <div class="field">
                    <label class="label">Llista d'alumnes</label>

                    @if ($alumnes->count() === 0)
                        <p style="color: rgba(255,255,255,.75); font-size: 14px;">
                            Aquest grup encara no té alumnes assignats.
                        </p>
                    @endif

                    <div class="alumnes-grid">
                        @foreach ($alumnes as $alumne)
                            <div class="alumne-card" style="cursor: default;">
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
                            </div>
                        @endforeach
                    </div>

                    <div class="pagination">
                        {{ $alumnes->links('vendor.pagination.three') }}
                    </div>
                </div>
                <div class="actions">
                    <a href="{{ route('espai.grups.index') }}" class="btn btn-secondary" style="width: 100%;">
                        Tornar
                    </a>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
