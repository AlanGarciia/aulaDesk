<x-app-layout>

     @push('styles')
        @vite('resources/css/espais/espaisIndex.css')
    @endpush

    <x-slot name="header">
        <div class="header-container">
            <h2 class="page-title">Editar espai</h2>
        </div>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form id="editForm" method="POST" action="{{ route('espais.update', $espai) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom"
                               value="{{ old('nom', $espai->nom) }}"
                               required>
                        @error('nom')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcio">Descripció (opcional)</label>
                        <textarea id="descripcio" name="descripcio" rows="4">{{ old('descripcio', $espai->descripcio) }}</textarea>
                        @error('descripcio')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-between items-center mt-6 gap-4 flex-wrap">
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Guardar canvis
                            </button>
                            <a href="{{ route('espais.index') }}" class="btn btn-secondary">
                                Tornar
                            </a>
                        </div>

                        <!-- Botón eliminar -->
                        <button type="button" class="btn btn-danger" id="deleteBtn">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Delete -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p id="confirmText">Segur que vols eliminar aquest espai?</p>
            <div class="modal-actions">
                <button id="cancelBtn" class="btn btn-secondary">Cancel·lar</button>
                <form id="deleteForm" method="POST" action="{{ route('espais.destroy', $espai) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ===== MODAL DELETE =====
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
            if(e.target === modal) modal.style.display = 'none';
        });
    </script>
    @endpush
</x-app-layout>
