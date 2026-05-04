<x-app-layout>
    <a href="{{ route('espai.index') }}" class="btn btn-secondary btn-top-right">
        Tornar a l'espai
    </a>

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">Notificacions</h2>
            </div>
        </div>
    </x-slot>

    <style>
        .notif-page { max-width: 820px; margin: 0 auto; padding: 24px 16px 60px; }
        .notif-page__title { font-size: 1.6rem; font-weight: 600; color: #1c1c1e; margin-bottom: 4px; }
        .notif-page__sub { color: #6b7280; font-size: 14px; margin-bottom: 18px; }
        .notif-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .notif-filters .chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 999px;
            border: 1px solid rgba(0,0,0,0.10); background: #fff;
            color: #374151; font-size: 13px; text-decoration: none;
            transition: background .12s;
        }
        .notif-filters .chip:hover { background: #f3f4f6; }
        .notif-filters .chip--active { background: #2563eb; color: #fff; border-color: #1d4ed8; }
        .notif-filters .chip--active:hover { background: #1d4ed8; }

        .notif-card {
            background: #fff; border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px; box-shadow: 0 4px 14px rgba(0,0,0,0.04); overflow: hidden;
        }
        .notif-row {
            display: flex; gap: 12px; padding: 14px 18px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            text-decoration: none; color: inherit;
        }
        .notif-row:last-child { border-bottom: 0; }
        .notif-row:hover { background: rgba(0,0,0,0.02); }
        .notif-row--unread { background: rgba(37,99,235,0.04); }

        .notif-row__icon {
            flex: 0 0 36px; width: 36px; height: 36px; border-radius: 50%;
            background: #eff6ff; color: #2563eb;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .notif-row--noticia .notif-row__icon { background: #fee2e2; color: #dc2626; }
        .notif-row--usuari  .notif-row__icon { background: #f3e8ff; color: #7c3aed; }
        .notif-row--guardia-sol .notif-row__icon { background: #fef3c7; color: #d97706; }
        .notif-row--guardia-acc .notif-row__icon { background: #dcfce7; color: #16a34a; }

        .notif-row__body { flex: 1; min-width: 0; }
        .notif-row__title { font-weight: 600; color: #111827; font-size: 14px; }
        .notif-row__msg { color: #4b5563; font-size: 13px; margin-top: 3px; }
        .notif-row__time { color: #9ca3af; font-size: 11px; margin-top: 4px; }
        .notif-row__dot { width: 9px; height: 9px; border-radius: 50%; background: #2563eb; margin-top: 14px; flex: 0 0 9px; }

        .notif-empty { padding: 60px 20px; text-align: center; color: #6b7280; }
        .notif-empty i { font-size: 48px; color: #d1d5db; display: block; margin-bottom: 12px; }

        .notif-page__pagination { margin-top: 20px; }

        @media (prefers-color-scheme: dark) {
            .notif-page__title { color: #f3f4f6; }
            .notif-card { background: #1f1f22; border-color: rgba(255,255,255,0.08); }
            .notif-row__title { color: #f3f4f6; }
            .notif-row__msg { color: #d1d5db; }
            .notif-row:hover { background: rgba(255,255,255,0.04); }
            .notif-filters .chip { background: #2a2a2d; color: #e5e7eb; border-color: rgba(255,255,255,0.10); }
            .notif-filters .chip:hover { background: #34343a; }
        }
    </style>

    <div class="notif-page">
        <h1 class="notif-page__title">Totes les notificacions</h1>
        <p class="notif-page__sub">Aquí tens l'historial complet d'avisos del teu espai.</p>

        @php
            $tipusOptions = [
                '' => 'Tots els tipus',
                'noticia_creada' => 'Notícies',
                'usuari_nou' => 'Usuaris nous',
                'guardia_solicitada' => 'Sol·licituds de guàrdia',
                'guardia_acceptada' => 'Guàrdies cobertes',
            ];

            $iconaPerTipus = fn($t) => match ($t) {
                'noticia_creada' => 'bi-journal-text',
                'usuari_nou' => 'bi-person-plus',
                'guardia_solicitada' => 'bi-clock-history',
                'guardia_acceptada' => 'bi-check2-circle',
                default => 'bi-bell',
            };

            $classePerTipus = fn($t) => match ($t) {
                'noticia_creada' => 'notif-row--noticia',
                'usuari_nou' => 'notif-row--usuari',
                'guardia_solicitada' => 'notif-row--guardia-sol',
                'guardia_acceptada' => 'notif-row--guardia-acc',
                default => '',
            };
        @endphp

        <div class="notif-filters" role="tablist" aria-label="Filtres">
            <a href="{{ route('espai.notificacions.index', ['filtre' => 'totes', 'tipus' => $tipusSeleccionat]) }}"
               class="chip {{ $filtre === 'totes' ? 'chip--active' : '' }}">Totes</a>
            <a href="{{ route('espai.notificacions.index', ['filtre' => 'no_llegides', 'tipus' => $tipusSeleccionat]) }}"
               class="chip {{ $filtre === 'no_llegides' ? 'chip--active' : '' }}">No llegides</a>
            <a href="{{ route('espai.notificacions.index', ['filtre' => 'llegides', 'tipus' => $tipusSeleccionat]) }}"
               class="chip {{ $filtre === 'llegides' ? 'chip--active' : '' }}">Llegides</a>

            <span style="flex:1"></span>

            @foreach($tipusOptions as $val => $label)
                <a href="{{ route('espai.notificacions.index', ['filtre' => $filtre, 'tipus' => $val]) }}"
                   class="chip {{ $tipusSeleccionat === $val ? 'chip--active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="notif-card">
            @forelse($notificacions as $n)
                <a href="{{ $n->url ?: '#' }}"
                   class="notif-row {{ $classePerTipus($n->tipus) }} {{ $n->llegida_el ? '' : 'notif-row--unread' }}">
                    <span class="notif-row__icon">
                        <i class="bi {{ $iconaPerTipus($n->tipus) }}" aria-hidden="true"></i>
                    </span>
                    <div class="notif-row__body">
                        <div class="notif-row__title">{{ $n->titol }}</div>
                        @if($n->missatge)
                            <div class="notif-row__msg">{{ $n->missatge }}</div>
                        @endif
                        <div class="notif-row__time">
                            {{ optional($n->created_at)->diffForHumans() }}
                            @if($n->llegida_el)
                                · llegida {{ $n->llegida_el->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    @unless($n->llegida_el)
                        <span class="notif-row__dot" aria-label="No llegida"></span>
                    @endunless
                </a>
            @empty
                <div class="notif-empty">
                    <i class="bi bi-bell-slash" aria-hidden="true"></i>
                    <div>No hi ha notificacions per als filtres seleccionats.</div>
                </div>
            @endforelse
        </div>

        <div class="notif-page__pagination">
            {{ $notificacions->links() }}
        </div>
    </div>
</x-app-layout>