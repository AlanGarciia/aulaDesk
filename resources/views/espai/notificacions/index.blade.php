@push('styles')
    @vite('resources/css/espai/notificacions/notificacion.css')
@endpush

<x-app-layout>
    <a href="{{ route('espai.index') }}" class="btn btn-secondary btn-top-right">
        {{ __('messages.back_to_space') }}
    </a>

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">{{ __('messages.notifications') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="notif-page">
        <h1 class="notif-page__title">{{ __('messages.notifications_all_title') }}</h1>
        <p class="notif-page__sub">{{ __('messages.notifications_sub') }}</p>

        @php
            $tipusOptions = [
                '' => __('messages.notif_type_all'),
                'noticia_creada' => __('messages.notif_type_news'),
                'usuari_nou' => __('messages.notif_type_new_users'),
                'guardia_solicitada' => __('messages.notif_type_guardia_requests'),
                'guardia_acceptada' => __('messages.notif_type_guardia_covered'),
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

        <div class="notif-filters" role="tablist" aria-label="{{ __('messages.filters') }}">
            <a href="{{ route('espai.notificacions.index', ['filtre' => 'totes', 'tipus' => $tipusSeleccionat]) }}"
               class="chip {{ $filtre === 'totes' ? 'chip--active' : '' }}">{{ __('messages.notif_all') }}</a>
            <a href="{{ route('espai.notificacions.index', ['filtre' => 'no_llegides', 'tipus' => $tipusSeleccionat]) }}"
               class="chip {{ $filtre === 'no_llegides' ? 'chip--active' : '' }}">{{ __('messages.notif_unread') }}</a>
            <a href="{{ route('espai.notificacions.index', ['filtre' => 'llegides', 'tipus' => $tipusSeleccionat]) }}"
               class="chip {{ $filtre === 'llegides' ? 'chip--active' : '' }}">{{ __('messages.notif_read') }}</a>

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
                            {{ optional($n->created_at)->locale(app()->getLocale())->diffForHumans() }}
                            @if($n->llegida_el)
                                · {{ __('messages.notif_read_at') }} {{ $n->llegida_el->locale(app()->getLocale())->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    @unless($n->llegida_el)
                        <span class="notif-row__dot" aria-label="{{ __('messages.notif_unread') }}"></span>
                    @endunless
                </a>
            @empty
                <div class="notif-empty">
                    <i class="bi bi-bell-slash" aria-hidden="true"></i>
                    <div>{{ __('messages.notif_empty') }}</div>
                </div>
            @endforelse
        </div>

        <div class="notif-page__pagination">
            {{ $notificacions->links() }}
        </div>
    </div>
</x-app-layout>