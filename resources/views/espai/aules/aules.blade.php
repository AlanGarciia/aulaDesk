@push('styles')
    @vite('resources/css/espai/aules/aulaIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Aules</h2>

                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}">
                        <i class="bi bi-box-arrow-right"></i>Tornar a l'espai
                    </a>
                    <a class="btn btn-primary" href="{{ route('espai.aules.create') }}">
                        Nova aula
                    </a>
                    <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">
                        Veure franges
                    </a>
                </div>
            </div>

            @if(session('ok'))
                <div id="successModal" class="modal-overlay">
                    <div class="modal-box">
                        {{ session('ok') }}
                        <button type="button" class="btn btn-secondary modal-close"
                                onclick="document.getElementById('successModal').style.display='none'">
                            Tancar
                        </button>
                    </div>
                </div>
            @endif

            <form method="GET" action="{{ route('espai.aules.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" value="{{ request('nom') }}" placeholder="Buscar per nom">
                    </div>

                    <div class="filter-group">
                        <label for="codi">Codi</label>
                        <input type="text" name="codi" id="codi" value="{{ request('codi') }}" placeholder="Buscar per codi">
                    </div>

                    <div class="filter-group">
                        <label for="planta">Planta</label>
                        <input type="text" name="planta" id="planta" value="{{ request('planta') }}" placeholder="Buscar per planta">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('espai.aules.index') }}" class="btn btn-secondary">Netejar</a>
                    </div>
                </div>
            </form>

            <div class="aules-grid">
                @forelse($aules as $aula)
                    <div class="aula-card">
                        <div class="aula-name">{{ $aula->nom }}</div>
                        <div class="aula-meta">Codi: {{ $aula->codi }}</div>
                        <div class="aula-meta">Capacitat: {{ $aula->capacitat }}</div>
                        <div class="aula-meta">Planta: {{ $aula->planta }}</div>

                        <div class="aula-actions">
                            <a class="btn btn-secondary @cantEspaiClass('aulas.manage')"
                               href="{{ route('espai.aules.admin', $aula) }}">
                                Administrar
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
                        <div>No hi ha aules disponibles.</div>
                        <a class="btn btn-primary @cantEspaiClass('aulas.create')"
                           href="{{ route('espai.aules.create') }}">
                            Crear primera aula
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="pagination">
                {{ $aules->links() }}
            </div>

        </div>
    </div>

    {{-- ============================
         🚨 MODAL LÍMIT AULES (PLAN FREE)
       ============================ --}}
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
                    Límit d’aules assolit
                </h2>

                <p class="mt-3 text-center text-gray-600">
                    El pla gratuït només permet crear fins a
                    <span class="font-semibold">10 aules</span>.
                </p>

                <p class="mt-1 text-center text-sm text-gray-500">
                    Millora el teu pla per poder crear més aules.
                </p>

                <div class="mt-6 flex gap-3">

                    <button onclick="closePlanModal()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Tancar
                    </button>

                    <a href="{{ route('stripe.checkout.premium') }}"
                       class="w-full px-4 py-3 rounded-xl bg-indigo-600 text-white text-center hover:bg-indigo-700 transition">
                        Millorar pla
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