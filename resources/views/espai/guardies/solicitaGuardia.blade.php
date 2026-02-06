{{-- resources/views/espai/guardies/solicitaGuardia.blade.php --}}

@push('styles')
<style>
    .guardies-wrap { max-width: 900px; margin: 0 auto; }
    .card {
        background: #fff;
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .head {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-bottom: 10px;
    }

    .title { margin:0; font-size: 1.25rem; font-weight: 800; }
    .sub { margin:6px 0 0; color: rgba(0,0,0,.65); }

    .row { display:flex; gap:12px; flex-wrap:wrap; margin-top: 14px; }
    .field { flex:1; min-width: 220px; }
    label { display:inline-block; font-weight: 800; font-size: .92rem; margin-bottom: 6px; color: rgba(0,0,0,.75); }

    input[type="text"],
    select,
    textarea {
        width: 100%;
        padding: 10px 10px;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,.14);
        background: #fff;
        font-weight: 650;
        font-size: .95rem;
        outline: none;
        transition: box-shadow .12s ease, border-color .12s ease;
    }

    textarea { resize: vertical; min-height: 100px; }

    input:focus, select:focus, textarea:focus {
        border-color: rgba(59, 130, 246, .45);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .12);
    }

    .actions {
        display:flex;
        gap:10px;
        justify-content:flex-end;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .btn {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,.12);
        background: #fff;
        color: rgba(0,0,0,.85);
        text-decoration:none;
        font-weight: 800;
        font-size: .92rem;
        line-height: 1;
        cursor: pointer;
        transition: transform .06s ease, box-shadow .12s ease, background .12s ease;
        user-select: none;
    }

    .btn:hover {
        background: rgba(0,0,0,.03);
        box-shadow: 0 10px 18px rgba(0,0,0,.06);
        transform: translateY(-1px);
    }

    .btn:active { transform: translateY(0); box-shadow: none; }

    .btn-primary {
        border-color: rgba(59, 130, 246, .35);
        background: rgba(59, 130, 246, .12);
        color: rgba(30, 64, 175, .95);
    }

    .btn-primary:hover { background: rgba(59, 130, 246, .16); }

    .btn-secondary { background: rgba(0,0,0,.04); }

    .alert {
        border: 1px solid rgba(0,0,0,.12);
        background: rgba(0,0,0,.03);
        color: rgba(0,0,0,.75);
        padding: 10px 12px;
        border-radius: 12px;
        margin: 10px 0 14px;
    }

    .error { margin-top: 6px; font-size: .9rem; font-weight: 800; color: rgba(185, 28, 28, .95); }

    .pill {
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .85rem;
        border: 1px solid rgba(0,0,0,.08);
        background: rgba(0,0,0,.02);
        color: rgba(0,0,0,.75);
    }

    @media (max-width: 768px) {
        .head { flex-direction: column; align-items:flex-start; }
        .actions { justify-content: stretch; }
        .actions .btn { width: 100%; }
    }
</style>
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
                        <p class="sub">Revisa el moment i explica breument el motiu (opcional).</p>
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

                {{-- 
                    Ruta: espai.guardia.solicitaGuardia
                    Si la quieres como GET + POST separado, normalmente:
                    - GET -> mostra el form
                    - POST -> guarda (p.e. espai.guardia.guardaSolicitud)
                    
                    Yo la dejo apuntando a una supuesta POST: espai.guardia.solicitaGuardia
                    Si al final la POST tiene otro nombre, cambia el route() aquí.
                --}}
                <form method="POST" action="{{ route('espai.guardia.solicitaGuardia') }}">
                    @csrf

                    {{-- Mantener contexto del slot --}}
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