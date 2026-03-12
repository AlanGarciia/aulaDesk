@push('styles')
    @vite('resources/css/espai/guardies/solicitaGuardia.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="guardies-wrap">
            <h2 class="page-title" style="margin:0;">Sol·licitar guàrdia</h2>
        </div>
    </x-slot>

    <div class="page">
        <div class="guardies-wrap">

            <p style="margin: 0 0 12px;">
                <a class="btn btn-secondary" href="{{ route('espai.guardies.index') }}">Tornar</a>
            </p>

            @if(session('ok'))
                <div class="alert" style="border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.10); color: rgba(14,89,38,.95);">
                    {{ session('ok') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert">
                    <strong>Hi ha errors:</strong>
                    <ul style="margin: 8px 0 0 18px;">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $diaTxt = '';
                $franjaTxt = '';
                $aulaTxt = '';

                if (isset($diaLabel) && $diaLabel) {
                    $diaTxt = (string) $diaLabel;
                } else {
                    if (isset($diesLabels) && isset($diesLabels[$dia])) {
                        $diaTxt = (string) $diesLabels[$dia];
                    } else {
                        if (isset($dia)) $diaTxt = 'Dia ' . (string) $dia;
                    }
                }

                if (isset($franjaLabel) && $franjaLabel) {
                    $franjaTxt = (string) $franjaLabel;
                } else {
                    if (isset($franja) && isset($franja->inici) && isset($franja->fi)) {
                        $franjaTxt = substr((string)$franja->inici, 0, 5) . ' - ' . substr((string)$franja->fi, 0, 5);
                    }
                }

                if (isset($aulaNom) && $aulaNom) {
                    $aulaTxt = (string) $aulaNom;
                } else {
                    if (isset($aula) && isset($aula->nom) && $aula->nom) {
                        $aulaTxt = (string) $aula->nom;
                    }
                }
            @endphp

            <div class="card">
                <div class="head">
                    <div>
                        <h3 class="title">Sol·licitud de guàrdia</h3>
                    </div>

                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        @if($diaTxt !== '')
                            <span class="pill">📅 {{ $diaTxt }}</span>
                        @endif
                        @if($franjaTxt !== '')
                            <span class="pill">⏰ {{ $franjaTxt }}</span>
                        @endif
                        @if($aulaTxt !== '')
                            <span class="pill">🚪 {{ $aulaTxt }}</span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('espai.guardia.solicitaGuardia') }}">
                    @csrf

                    <input type="hidden" name="dia" value="{{ isset($dia) ? (string)$dia : '' }}">
                    <input type="hidden" name="franja_id" value="{{ isset($franjaId) ? (string)$franjaId : (isset($franja) && isset($franja->id) ? (string)$franja->id : '') }}">

                    <div class="row">
                        <div class="field">
                            <label>Dia</label>
                            <input type="text" value="{{ $diaTxt }}" readonly>
                        </div>

                        <div class="field">
                            <label>Franja</label>
                            <input type="text" value="{{ $franjaTxt }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="field">
                            <label>Aula</label>
                            <input type="text" value="{{ $aulaTxt }}" readonly>
                        </div>

                        <div class="field">
                            <label>Tipus (opcional)</label>
                            <select name="tipus">
                                @php
                                    $tipusOld = old('tipus');
                                    if ($tipusOld === null) $tipusOld = '';
                                @endphp
                                <option value="" {{ $tipusOld === '' ? 'selected' : '' }}>—</option>
                                <option value="canvi" {{ $tipusOld === 'canvi' ? 'selected' : '' }}>Canvi</option>
                                <option value="absencia" {{ $tipusOld === 'absencia' ? 'selected' : '' }}>Absència</option>
                                <option value="suport" {{ $tipusOld === 'suport' ? 'selected' : '' }}>Suport</option>
                                <option value="altres" {{ $tipusOld === 'altres' ? 'selected' : '' }}>Altres</option>
                            </select>
                            @error('tipus') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <label>Comentari (opcional)</label>
                        <textarea name="comentari" placeholder="Ex: Necessito cobertura perquè...">{{ old('comentari') }}</textarea>
                        @error('comentari') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('espai.guardies.index') }}">Cancel·lar</a>
                        <button class="btn btn-primary" type="submit">Enviar sol·licitud</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>