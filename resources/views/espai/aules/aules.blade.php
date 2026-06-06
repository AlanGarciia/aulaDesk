@push('styles')
    @vite('resources/css/espai/aules/aulaIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">{{ __('messages.classrooms') }}</h2>

                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}">
                        <i class="bi bi-box-arrow-right"></i>{{ __('messages.back_to_space') }}
                    </a>
                    <a class="btn btn-primary" href="{{ route('espai.aules.create') }}">
                        {{ __('messages.new_classroom') }}
                    </a>
                    <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">
                        {{ __('messages.view_slots') }}
                    </a>
                </div>
            </div>

            @if(session('ok'))
                <div id="successModal" class="modal-overlay">
                    <div class="modal-box">
                        {{ session('ok') }}
                        <button type="button" class="btn btn-secondary modal-close"
                                onclick="document.getElementById('successModal').style.display='none'">
                            {{ __('messages.close') }}
                        </button>
                    </div>
                </div>
            @endif

            <form method="GET" action="{{ route('espai.aules.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="nom">{{ __('messages.name') }}</label>
                        <input type="text" name="nom" id="nom" value="{{ request('nom') }}" placeholder="{{ __('messages.search_by_name') }}">
                    </div>

                    <div class="filter-group">
                        <label for="codi">{{ __('messages.code') }}</label>
                        <input type="text" name="codi" id="codi" value="{{ request('codi') }}" placeholder="{{ __('messages.search_by_code') }}">
                    </div>

                    <div class="filter-group">
                        <label for="planta">{{ __('messages.floor') }}</label>
                        <input type="text" name="planta" id="planta" value="{{ request('planta') }}" placeholder="{{ __('messages.search_by_floor') }}">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                        <a href="{{ route('espai.aules.index') }}" class="btn btn-secondary">{{ __('messages.clear') }}</a>
                    </div>
                </div>
            </form>

            <div class="aules-grid">
                @forelse($aules as $aula)
                    <div class="aula-card">
                        <div class="aula-name">{{ $aula->nom }}</div>
                        <div class="aula-meta">{{ __('messages.code') }}: {{ $aula->codi }}</div>
                        <div class="aula-meta">{{ __('messages.capacity') }}: {{ $aula->capacitat }}</div>
                        <div class="aula-meta">{{ __('messages.floor') }}: {{ $aula->planta }}</div>

                        <div class="aula-actions">
                            <a class="btn btn-secondary @cantEspaiClass('aulas.manage')"
                               href="{{ route('espai.aules.admin', $aula) }}">
                                {{ __('messages.manage') }}
                            </a>

                            <a class="btn btn-secondary @cantEspaiClass('tickets.view')"
                               href="{{ route('espai.aules.tickets.index', $aula) }}">
                                <i class="bi bi-ticket-detailed"></i> {{ __('messages.tickets') }}
                            </a>

                            <a class="btn btn-secondary btn-icon @cantEspaiClass('aulas.update')"
                               href="{{ route('espai.aules.edit', $aula) }}">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.aules.destroy', $aula) }}">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-icon @cantEspaiClass('aulas.delete')"
                                        type="submit">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div>{{ __('messages.classrooms_empty') }}</div>
                        <a class="btn btn-primary @cantEspaiClass('aulas.create')"
                           href="{{ route('espai.aules.create') }}">
                            {{ __('messages.create_first_classroom') }}
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="pagination">
                {{ $aules->links() }}
            </div>

        </div>
    </div>

    @if(session('showLimitModal'))
        <div id="planModal"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">

            <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 animate-fadeIn">

                <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-full bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-8 h-8 text-red-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </div>

                <h2 class="mt-4 text-2xl font-bold text-center text-gray-800">
                    {{ __('messages.classroom_limit_title') }}
                </h2>

                <p class="mt-3 text-center text-gray-600">
                    {!! __('messages.classroom_limit_text') !!}
                </p>

                <p class="mt-1 text-center text-sm text-gray-500">
                    {{ __('messages.classroom_limit_upgrade') }}
                </p>

                <div class="mt-6 flex gap-3">

                    <button onclick="closePlanModal()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        {{ __('messages.close') }}
                    </button>

                    <a href="{{ route('stripe.checkout.premium') }}"
                       class="w-full px-4 py-3 rounded-xl bg-indigo-600 text-white text-center hover:bg-indigo-700 transition">
                        {{ __('messages.upgrade_plan') }}
                    </a>

                </div>

            </div>
        </div>

        <script>
            function closePlanModal() {
                document.getElementById('planModal').style.display = 'none';
            }
        </script>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(.95); }
                to { opacity: 1; transform: scale(1); }
            }

            .animate-fadeIn {
                animation: fadeIn .2s ease-out;
            }
        </style>
    @endif

</x-app-layout>