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
                <p class="page-subtitle">{{ __('messages.admin_panel') }}</p>

                <a href="{{ route('espais.index') }}" class="btn-exit" aria-label="{{ __('messages.exit') }}">
                    <i class="bi bi-box-arrow-right icon" aria-hidden="true"></i>
                    <span>{{ __('messages.exit') }}</span>
                </a>
            </header>

            <nav class="icon-btns" aria-label="{{ __('messages.space_options') }}">

                @canEspai('groups.view')
                <a href="{{ route('espai.grups.index') }}" class="icon-btn" aria-label="{{ __('messages.groups') }}">
                    <span class="icon-btn__back bg-blue" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-people-fill" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.groups') }}</span>
                </a>
                @endcanEspai

                @canEspai('users.view')
                <a href="{{ route('espai.usuaris.index') }}" class="icon-btn" aria-label="{{ __('messages.view_users') }}">
                    <span class="icon-btn__back bg-purple" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-people" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.view_users') }}</span>
                </a>
                @endcanEspai

                @canEspai('noticies.view')
                <a href="{{ route('espai.noticies.index') }}" class="icon-btn" aria-label="{{ __('messages.news_board_title') }}">
                    <span class="icon-btn__back bg-red" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-journal-text" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.news_board_title') }}</span>
                </a>
                @endcanEspai

                @canEspai('aulas.view')
                <a href="{{ route('espai.aules.index') }}" class="icon-btn" aria-label="{{ __('messages.classrooms') }}">
                    <span class="icon-btn__back bg-indigo" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-building" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.classrooms') }}</span>
                </a>
                @endcanEspai

                @canEspai('students.view')
                <a href="{{ route('espai.alumnes.index') }}" class="icon-btn" aria-label="{{ __('messages.students') }}">
                    <span class="icon-btn__back bg-orange" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-person-vcard" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.students') }}</span>
                </a>
                @endcanEspai

                @canEspai('guardies.view')
                <a href="{{ route('espai.guardies.index') }}" class="icon-btn" aria-label="{{ __('messages.calendar') }}">
                    <span class="icon-btn__back bg-green" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-calendar-check" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.calendar') }}</span>
                </a>
                @endcanEspai

                @canEspai('tickets.view')
                <a href="{{ route('espai.tickets.all') }}" class="icon-btn" aria-label="{{ __('messages.tickets') }}">
                    <span class="icon-btn__back bg-pink" aria-hidden="true"></span>
                    <span class="icon-btn__front" aria-hidden="true">
                        <span class="icon-btn__icon" aria-hidden="true">
                            <i class="bi bi-ticket-detailed" aria-hidden="true"></i>
                        </span>
                    </span>
                    <span class="icon-btn__label">{{ __('messages.tickets') }}</span>
                </a>
                @endcanEspai

            </nav>
        </div>
    </div>
</x-app-layout>