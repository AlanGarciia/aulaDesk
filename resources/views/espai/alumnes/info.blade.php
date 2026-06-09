@push('styles')
    @vite('resources/css/espai/alumnes/infoAlumnes.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">{{ __('messages.student_info_title') }}</h2>
                <div class="header-actions">
                    <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                    </a>
                    <a href="{{ route('espai.alumnes.edit', $alumne) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> {{ __('messages.edit') }}
                    </a>
                    <a href="{{ route('espai.alumnes.pdf', $alumne) }}" class="btn btn-secondary">
                        <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.download_pdf') }}
                    </a>
                </div>
            </div>

            {{-- DADES DE L'ALUMNE --}}
            <div class="info-card">
                <div class="info-card__head">
                    <div class="info-avatar">{{ mb_strtoupper(mb_substr($alumne->nom, 0, 1)) }}</div>
                    <div>
                        <h3 class="info-name">{{ $alumne->nomFormatat() }}</h3>
                        <span class="info-sub">{{ __('messages.idalu') }}: {{ $alumne->idalu }}</span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.name') }}</span>
                        <span class="info-value">{{ $alumne->nom }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.surname1') }}</span>
                        <span class="info-value">{{ $alumne->cognom1 ?: '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.surname2') }}</span>
                        <span class="info-value">{{ $alumne->cognom2 ?: '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.birth_date') }}</span>
                        <span class="info-value">
                            @if($alumne->data_naixement)
                                {{ $alumne->data_naixement->format('d/m/Y') }}
                                <span class="age-badge">{{ __('messages.years_old', ['age' => $alumne->data_naixement->age]) }}</span>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.dni') }}</span>
                        <span class="info-value">{{ $alumne->dni ?: '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.email') }}</span>
                        <span class="info-value">{{ $alumne->correu ?: '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.phone') }}</span>
                        <span class="info-value">{{ $alumne->telefon ?: '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.assigned_groups') }}</span>
                        <span class="info-value">
                            @if($alumne->grups->count())
                                @foreach($alumne->grups as $g)
                                    <span class="chip">{{ $g->nom }}</span>
                                @endforeach
                            @else
                                {{ __('messages.no_groups') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- TUTORS --}}
            <div class="tutors-section">
                <h3 class="section-title">
                    <i class="bi bi-people"></i> {{ __('messages.family_tutors') }}
                </h3>

                @if($alumne->tutors->count())
                    <div class="tutors-grid">
                        @foreach($alumne->tutors as $tutor)
                            <div class="tutor-card">
                                <div class="tutor-card__head">
                                    <span class="tutor-card__name">{{ $tutor->nom }} {{ $tutor->cognoms }}</span>
                                    @if($tutor->parentiu)
                                        <span class="tutor-card__badge">{{ $tutor->parentiu }}</span>
                                    @endif
                                </div>
                                <div class="tutor-card__body">
                                    <div class="tutor-line">
                                        <i class="bi bi-envelope"></i>
                                        <span>{{ $tutor->correu ?: '—' }}</span>
                                    </div>
                                    <div class="tutor-line">
                                        <i class="bi bi-telephone"></i>
                                        <span>{{ $tutor->telefon ?: '—' }}</span>
                                    </div>
                                    <div class="tutor-line">
                                        <i class="bi bi-person-vcard"></i>
                                        <span>{{ $tutor->dni ?: '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="tutors-empty">{{ __('messages.no_tutors') }}</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>