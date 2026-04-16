@push('styles')
    @vite('resources/css/espai/espaiIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <header class="page-header">
                <p class="page-subtitle">Panell d'administrador</p>

                <a href="{{ route('espais.index') }}" class="btn-exit" aria-label="Sortir">
                    <i class="bi bi-box-arrow-right icon" aria-hidden="true"></i>
                    <span>Sortir</span>
                </a>
            </header>

            <nav class="icon-btns" aria-label="Opcions de l'espai">

                {{-- 🔵 GRUPS --}}
                @canEspai('groups.view')
                <a href="{{ route('espai.grups.index') }}" class="icon-btn" aria-label="Grups">
                    <span class="icon-btn__back bg-blue" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-people-fill" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">Grups</span>
                </a>
                @endcanEspai

                {{-- 🟣 USUARIS --}}
                @canEspai('users.view')
                <a href="{{ route('espai.usuaris.index') }}" class="icon-btn" aria-label="Veure usuaris">
                    <span class="icon-btn__back bg-purple" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-people" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">Veure usuaris</span>
                </a>
                @endcanEspai

                {{-- 🔴 NOTÍCIES --}}
                @canEspai('noticies.view')
                <a href="{{ route('espai.noticies.index') }}" class="icon-btn" aria-label="Tauló de notícies">
                    <span class="icon-btn__back bg-red" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-journal-text" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">Tauló de notícies</span>
                </a>
                @endcanEspai

                {{-- 🟦 AULES --}}
                @canEspai('aulas.view')
                <a href="{{ route('espai.aules.index') }}" class="icon-btn" aria-label="Aules">
                    <span class="icon-btn__back bg-indigo" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-building" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">Aules</span>
                </a>
                @endcanEspai

                {{-- 🟧 ALUMNES --}}
                @canEspai('students.view')
                <a href="{{ route('espai.alumnes.index') }}" class="icon-btn" aria-label="Alumnes">
                    <span class="icon-btn__back bg-orange" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-person-vcard" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">Alumnes</span>
                </a>
                @endcanEspai

                {{-- 🟩 GUARDIES --}}
                @canEspai('guardies.view')
                <a href="{{ route('espai.guardies.index') }}" class="icon-btn" aria-label="Guardies">
                    <span class="icon-btn__back bg-green" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-calendar-check" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">Guardies</span>
                </a>
                @endcanEspai

            </nav>
        </div>
    </div>
</x-app-layout>