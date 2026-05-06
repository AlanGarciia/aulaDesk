@push('styles')
    @vite('resources/css/espai/incidencies/incidencies.css')
@endpush

<x-app-layout>
    <a href="{{ route('espai.guardies.index') }}" class="btn btn-secondary btn-top-right">
        <i class="bi bi-arrow-left"></i> Tornar
    </a>

    <x-slot name="header">
        <div class="page-header">
            <h2 class="page-title">Passar llista</h2>
        </div>
    </x-slot>

    <div class="att-page">
        <div class="att-head">
            <h1>{{ $aulaHorari->grup->nom ?? 'Grup' }}</h1>
            <div class="att-head__meta">
                <span><i class="bi bi-door-open"></i>{{ $aulaHorari->aula->nom ?? '—' }}</span>
                <span><i class="bi bi-clock"></i>
                    {{ substr($aulaHorari->franja->inici ?? '', 0, 5) }} -
                    {{ substr($aulaHorari->franja->fi ?? '', 0, 5) }}
                </span>
                <span><i class="bi bi-calendar3"></i>{{ \Carbon\Carbon::parse($data)->isoFormat('dddd D MMM YYYY') }}</span>
                <span><i class="bi bi-people"></i>{{ $alumnes->count() }} alumnes</span>
            </div>
        </div>

        <div class="att-bar">
            <form method="GET" style="display:flex; align-items:center; gap:10px; margin:0;">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" value="{{ $data }}" onchange="this.form.submit()">
            </form>

            <a href="{{ route('espai.incidencies.pdf', ['aulaHorari' => $aulaHorari->id, 'from' => $data, 'to' => $data]) }}"
               class="att-pdf-btn"
               target="_blank"
               title="Descarregar PDF del dia">
                <i class="bi bi-file-earmark-pdf"></i> PDF dia
            </a>

            <div class="att-legend">
                <span><i class="bi {{ $tipusIcones['assistencia'] }} lg-as"></i>Assistència</span>
                <span><i class="bi {{ $tipusIcones['deures'] }} lg-de"></i>Deures</span>
                <span><i class="bi {{ $tipusIcones['material'] }} lg-ma"></i>Material</span>
                <span><i class="bi {{ $tipusIcones['amonestacio'] }} lg-am"></i>Amonestació</span>
            </div>
        </div>

        @if(session('ok'))
            <div class="att-flash">{{ session('ok') }}</div>
        @endif

        <form method="POST"
              action="{{ route('espai.incidencies.save', ['aulaHorari' => $aulaHorari->id]) }}">
            @csrf
            <input type="hidden" name="data" value="{{ $data }}">

            <div class="att-list">
                @forelse($alumnes as $alumne)
                    @php
                        $tipusActius = $incidencies->get($alumne->id, collect())->pluck('tipus')->toArray();
                    @endphp
                    <div class="att-row">
                        <div class="att-avatar">
                            {{ strtoupper(substr($alumne->nom ?? '?', 0, 1)) }}{{ strtoupper(substr($alumne->cognoms ?? '', 0, 1)) }}
                        </div>
                        <div class="att-name">
                            {{ trim(($alumne->cognoms ?? '') . ', ' . ($alumne->nom ?? '')) }}
                        </div>
                        <div class="att-toggles">
                            @foreach($tipusValids as $tipus)
                                <label class="toggle" data-tipus="{{ $tipus }}" title="{{ $tipusLabels[$tipus] }}">
                                    <input type="checkbox"
                                           name="selections[{{ $alumne->id }}][{{ $tipus }}]"
                                           value="1"
                                           {{ in_array($tipus, $tipusActius, true) ? 'checked' : '' }}>
                                    <span class="toggle__btn">
                                        <i class="bi {{ $tipusIcones[$tipus] }}"></i>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="att-row" style="justify-content:center; color:#6b7280;">
                        Aquest grup encara no té cap alumne.
                    </div>
                @endforelse
            </div>

            @if($alumnes->isNotEmpty())
                <div class="att-savebar">
                    <button type="submit" class="att-save">
                        <i class="bi bi-check-lg"></i> Guardar canvis
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>