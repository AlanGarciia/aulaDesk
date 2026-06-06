@push('styles')
    @vite('resources/css/espai/alumnes/infoAlumnes.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">{{ __('messages.student_info_title') }}</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                {{ __('messages.back') }}
            </a>

            <a href="{{ route('espai.alumnes.edit', $alumne) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i>
                {{ __('messages.edit') }}
            </a>
        </div>

        <div class="container">

            <div class="card user-info-card">

                <div class="info-row">
                    <span class="info-label">{{ __('messages.full_name') }}:</span>
                    <span class="info-value">{{ $alumne->nom }} {{ $alumne->cognoms }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">{{ __('messages.idalu') }}:</span>
                    <span class="info-value">{{ $alumne->idalu }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">{{ __('messages.email') }}:</span>
                    <span class="info-value">{{ $alumne->correu ?? '—' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">{{ __('messages.phone') }}:</span>
                    <span class="info-value">{{ $alumne->telefon ?? '—' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">{{ __('messages.assigned_groups') }}:</span>
                    <span class="info-value">
                        @if($alumne->grups->count())
                            {{ $alumne->grups->pluck('nom')->join(', ') }}
                        @else
                            {{ __('messages.no_groups') }}
                        @endif
                    </span>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>