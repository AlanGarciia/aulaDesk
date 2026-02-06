<x-app-layout>

    @push('styles')
        @vite('resources/css/espais/espaisEdit.css')
    @endpush

    <div class="page">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Editar espai</h2>

                <a href="{{ route('espais.index') }}" class="btn btn-secondary">
                    Tornar
                </a>
            </div>

            {{-- FORMULARIO COMO POST-IT GRANDE --}}
            <div class="edit-postit">
                <form id="editForm" method="POST" action="{{ route('espais.update', $espai) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="{{ old('nom', $espai->nom) }}"
                            required
                        >
                        @error('nom')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcio">Descripció (opcional)</label>
                        <textarea
                            id="descripcio"
                            name="descripcio"
                            rows="4"
                        >{{ old('descripcio', $espai->descripcio) }}</textarea>

                        @error('descripcio')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="edit-actions">
                        <button type="submit" class="btn btn-primary">
                            Guardar canvis
                        </button>

                        <button type="button" class="btn btn-danger" id="deleteBtn">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- MODAL ELIMINAR --}}
    <div id="confirmModal" class="modal">
        <div class="modal-content modal-delete">
            <p>Segur que vols eliminar aquest espai?</p>
            <div class="modal-actions">
                <button id="cancelBtn" class="btn btn-cancel">Cancel·lar</button>
                <form method="POST" action="{{ route('espais.destroy', $espai) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const deleteBtn = document.getElementById('deleteBtn');
        const modal = document.getElementById('confirmModal');
        const cancelBtn = document.getElementById('cancelBtn');

        deleteBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });

        cancelBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    </script>
    @endpush

</x-app-layout>
