<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Tauló de notícies</h2>
        @vite(['resources/css/espai/noticies/noticies.css'])

    </x-slot>

    <div class="page">
        <div class="container">
            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <div class="actions">
                <a class="btn btn-primary" href="{{ route('espai.noticies.create') }}">+ Nova notícia</a>
                <a class="btn btn-secondary" href="{{ route('espai.index') }}">Tornar a l'espai</a>
            </div>

            <div class="card">
                @forelse($noticies as $n)
                    <div class="user-row">
                        <div class="user-info">
                            <div class="user-name">{{ $n->titol }}</div>
                            <div class="user-meta">
                                Tipus: {{ $n->tipus }} · {{ $n->created_at->format('d/m/Y') }} · Reaccions: {{ $n->reaccions_count }}
                            </div>

                            @if($n->imatge_path)
                                <div style="margin-top:10px">
                                    <img src="{{ asset('storage/'.$n->imatge_path) }}" alt="imatge" style="max-width:240px;border-radius:10px;">
                                </div>
                            @endif

                            @if($n->contingut)
                                <p style="margin-top:10px">{{ $n->contingut }}</p>
                            @endif
                        </div>

                        <div class="user-actions">
                            {{-- Reacció exemple --}}
                            <form class="inline-form" method="POST" action="{{ route('espai.noticies.reaccio', $n) }}">
                                @csrf
                                <input type="hidden" name="tipus" value="like">
                                <button class="btn btn-secondary" type="submit">👍</button>
                            </form>

                            <form class="inline-form" method="POST" action="{{ route('espai.noticies.destroy', $n) }}"
                                  onsubmit="return confirm('Eliminar la notícia?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">Encara no hi ha notícies.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
