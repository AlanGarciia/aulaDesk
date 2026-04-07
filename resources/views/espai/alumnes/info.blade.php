@push('styles')
    @vite('resources/css/espai/alumnes/infoAlumnes.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Informació de l'alumne</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Tornar
            </a>

            <a href="{{ route('espai.alumnes.edit', $alumne) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i>
                Editar
            </a>
        </div>

        <div class="container">

            <div class="card user-info-card">

                <div class="info-row">
                    <span class="info-label">Nom complet:</span>
                    <span class="info-value">{{ $alumne->nom }} {{ $alumne->cognoms }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">IDALU:</span>
                    <span class="info-value">{{ $alumne->idalu }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Correu:</span>
                    <span class="info-value">{{ $alumne->correu ?? '—' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Telèfon:</span>
                    <span class="info-value">{{ $alumne->telefon ?? '—' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Grups assignats:</span>
                    <span class="info-value">
                        @if($alumne->grups->count())
                            {{ $alumne->grups->pluck('nom')->join(', ') }}
                        @else
                            Sense grups
                        @endif
                    </span>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
