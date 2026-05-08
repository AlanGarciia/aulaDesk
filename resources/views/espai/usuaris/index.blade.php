@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="page-title-container">
            <h2 class="page-title">Usuaris de l'espai</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary @cantEspaiClass('users.create')">
                + Afegir usuari
            </a>

            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                <i class="bi bi-box-arrow-right"></i>Tornar a l'espai
            </a>
        </div>

        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('espai.usuaris.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="nom">Nom</label>
                        <input type="text"
                               name="nom"
                               id="nom"
                               value="{{ request('nom') }}"
                               placeholder="Buscar per nom">
                    </div>

                    <div class="filter-group">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol">
                            <option value="">Tots</option>

                            @foreach(\App\Models\BaseRole::pluck('nom') as $rol)
                                <option value="{{ $rol }}"
                                    {{ request('rol') === $rol ? 'selected' : '' }}>
                                    {{ ucfirst($rol) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            Filtrar
                        </button>

                        <a href="{{ route('espai.usuaris.index') }}"
                           class="btn btn-secondary">
                            Netejar
                        </a>
                    </div>
                </div>
            </form>

            <div class="card">
                @forelse ($usuaris as $usuari)

                    <div class="user-row">
                        <div class="user-info">
                            <div class="user-name">
                                {{ $usuari->nom }}
                            </div>

                            <div class="user-meta">
                                Rol: {{ $usuari->rol }}
                            </div>
                        </div>

                        <div class="user-actions">

                            <a class="btn btn-secondary @cantEspaiClass('users.update')"
                               href="{{ route('espai.usuaris.roles', $usuari) }}">
                                <i class="bi bi-shield-check"></i>
                            </a>

                            <a class="btn btn-secondary @cantEspaiClass('users.update')"
                               href="{{ route('espai.usuaris.edit', $usuari) }}">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.usuaris.destroy', $usuari) }}"
                                  onsubmit="return confirm('Segur que vols eliminar aquest usuari?');">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger @cantEspaiClass('users.delete')">
                                    Eliminar
                                </button>
                            </form>

                        </div>
                    </div>

                @empty

                    <p class="empty-state">
                        No hi ha usuaris creats en aquest espai.
                    </p>

                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL LIMIT PLA GRATUÏT --}}
    @if(session('showPlanModal'))
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
                    Límit assolit
                </h2>

                <p class="mt-3 text-center text-gray-600">
                    El pla gratuït només permet crear fins a
                    <span class="font-semibold">3 usuaris</span>.
                </p>

                <p class="mt-1 text-center text-sm text-gray-500">
                    Millora el teu pla per afegir més membres a l’espai.
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
                from {
                    opacity: 0;
                    transform: scale(.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .animate-fadeIn {
                animation: fadeIn .2s ease-out;
            }
        </style>
    @endif

</x-app-layout>