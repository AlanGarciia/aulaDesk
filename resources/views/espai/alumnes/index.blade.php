@push('styles')
    @vite('resources/css/espai/alumnes/alumnesIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">

            {{-- Cabecera --}}
            <div class="page-header">
                <h2 class="page-title">{{ __('messages.students_index_title') }}</h2>
                <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                    <i class="bi bi-box-arrow-left"></i> {{ __('messages.back_to_space') }}
                </a>
            </div>

            {{-- Acciones --}}
            <div class="toolbar">
                <a href="{{ route('espai.alumnes.create') }}" class="btn btn-primary @cantEspaiClass('students.create')">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.students_add_title') }}
                </a>

                @if(auth()->user()->plan === 'premium')
                    <a href="{{ route('espai.alumnes.import.form') }}" class="btn btn-secondary">
                        <i class="bi bi-upload"></i> {{ __('messages.import_csv') }}
                    </a>
                    <a href="{{ route('espai.alumnes.export') }}" class="btn btn-secondary">
                        <i class="bi bi-download"></i> {{ __('messages.export_csv') }}
                    </a>
                @else
                    <button class="btn btn-secondary is-locked" disabled>
                        <i class="bi bi-upload"></i> {{ __('messages.import_csv') }} <span class="lock">🔒</span>
                    </button>
                    <button class="btn btn-secondary is-locked" disabled>
                        <i class="bi bi-download"></i> {{ __('messages.export_csv') }} <span class="lock">🔒</span>
                    </button>
                @endif

                <a href="{{ route('espai.grups.index') }}" class="btn btn-secondary @cantEspaiClass('groups.view')">
                    <i class="bi bi-people"></i> {{ __('messages.view_groups') }}
                </a>

                <form method="POST" action="{{ route('espai.alumnes.format') }}" class="format-selector">
                    @csrf
                    <label for="format_nom">{{ __('messages.name_format') }}</label>
                    <select name="format_nom" id="format_nom" onchange="this.form.submit()">
                        <option value="nom_cognoms" @selected(($espai->format_nom ?? 'nom_cognoms') === 'nom_cognoms')>
                            {{ __('messages.format_name_first') }}
                        </option>
                        <option value="cognoms_nom" @selected(($espai->format_nom ?? 'nom_cognoms') === 'cognoms_nom')>
                            {{ __('messages.format_surname_first') }}
                        </option>
                    </select>
                </form>
            </div>

            @if (session('ok'))
                <div class="alert-success">
                    <i class="bi bi-check-circle"></i> {{ session('ok') }}
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" action="{{ route('espai.alumnes.index') }}" class="filters">
                <div class="filters__field">
                    <input type="text" name="nom" value="{{ request('nom') }}" placeholder="{{ __('messages.search_by_name') }}">
                </div>
                <div class="filters__field">
                    <input type="text" name="cognoms" value="{{ request('cognoms') }}" placeholder="{{ __('messages.search_by_surname') }}">
                </div>
                <div class="filters__field">
                    <input type="text" name="idalu" value="{{ request('idalu') }}" placeholder="{{ __('messages.search_by_idalu') }}">
                </div>
                <div class="filters__field">
                    <input type="text" name="telefon" value="{{ request('telefon') }}" placeholder="{{ __('messages.search_by_phone') }}">
                </div>
                <div class="filters__field">
                    <select name="grup">
                        <option value="">{{ __('messages.all_groups') }}</option>
                        @foreach($grups as $g)
                            <option value="{{ $g->id }}" @selected(request('grup') == $g->id)>{{ $g->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filters__actions">
                    <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                    <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">{{ __('messages.clear') }}</a>
                </div>
            </form>

            <div class="results-count">
                @if($filtrats !== $totalAlumnes)
                    {{ __('messages.showing_filtered', ['shown' => $filtrats, 'total' => $totalAlumnes]) }}
                @else
                    {{ __('messages.showing_total', ['total' => $totalAlumnes]) }}
                @endif
            </div>

            {{-- FORM de borrado masivo: envuelve SOLO la tabla --}}
            <form method="POST" action="{{ route('espai.alumnes.destroyMultiple') }}" id="bulkForm">
                @csrf

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ta-center" style="width:42px;">
                                    <input type="checkbox" id="checkAll" class="row-check">
                                </th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.idalu') }}</th>
                                <th>{{ __('messages.email') }}</th>
                                <th>{{ __('messages.phone') }}</th>
                                <th class="ta-right">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($alumnes as $alumne)
                                <tr>
                                    <td class="ta-center" data-label="">
                                        <input type="checkbox" name="ids[]" value="{{ $alumne->id }}" class="row-check js-row-check">
                                    </td>
                                    <td data-label="{{ __('messages.name') }}">
                                        <span class="cell-strong">{{ $alumne->nomFormatat($espai->format_nom) }}</span>
                                    </td>
                                    <td data-label="{{ __('messages.idalu') }}">{{ $alumne->idalu }}</td>
                                    <td data-label="{{ __('messages.email') }}">{{ $alumne->correu ?: '—' }}</td>
                                    <td data-label="{{ __('messages.phone') }}">{{ $alumne->telefon ?: '—' }}</td>
                                    <td class="ta-right">
                                        <div class="row-menu">
                                            <button type="button" class="icon-btn js-row-menu-btn" aria-label="{{ __('messages.options') }}">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <div class="row-menu__dropdown">
                                                <a href="{{ route('espai.alumnes.edit', $alumne) }}"
                                                   class="row-menu__item @cantEspaiClass('students.update')">
                                                    <i class="bi bi-pencil-square"></i> {{ __('messages.edit') }}
                                                </a>
                                                <a href="{{ route('espai.alumnes.info', $alumne) }}"
                                                   class="row-menu__item @cantEspaiClass('students.view')">
                                                    <i class="bi bi-info-circle"></i> {{ __('messages.view') }}
                                                </a>
                                                <a href="{{ route('espai.alumnes.pdf', $alumne) }}"
                                                   class="row-menu__item @cantEspaiClass('students.view')">
                                                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.download_pdf') }}
                                                </a>
                                                <button type="button"
                                                        class="row-menu__item row-menu__item--danger @cantEspaiClass('students.delete')"
                                                        onclick="deleteOne('{{ route('espai.alumnes.destroy', $alumne) }}')">
                                                    <i class="bi bi-trash"></i> {{ __('messages.delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">{{ __('messages.students_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Barra flotante de selección --}}
                <div class="bulk-bar" id="bulkBar">
                    <span class="bulk-bar__count"><span id="bulkCount">0</span> {{ __('messages.selected') }}</span>
                    <div class="bulk-bar__actions">
                        <button type="button" class="btn btn-secondary" onclick="clearSelection()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> {{ __('messages.delete_selected') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- Form oculto para borrado individual --}}
            <form method="POST" id="deleteOneForm" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            <div class="pagination">{{ $alumnes->links() }}</div>

        </div>
    </div>

    {{-- MODAL confirmación borrado --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-box__icon"><i class="bi bi-exclamation-triangle"></i></div>
            <h3 class="modal-box__title">{{ __('messages.confirm_delete_title') }}</h3>
            <p class="modal-box__text" id="modalText"></p>
            <div class="modal-box__actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="modalConfirmBtn">
                    <i class="bi bi-trash"></i> {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // ===== MODAL (global, fuera de DOMContentLoaded) =====
    const deleteModal = document.getElementById('deleteModal');
    const modalText = document.getElementById('modalText');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    let modalAction = null;

    function openDeleteModal(text, onConfirm) {
        modalText.textContent = text;
        modalAction = onConfirm;
        deleteModal.classList.add('is-open');
    }

    function closeDeleteModal() {
        deleteModal.classList.remove('is-open');
        modalAction = null;
    }

    modalConfirmBtn.addEventListener('click', () => {
        if (typeof modalAction === 'function') modalAction();
    });
    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) closeDeleteModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDeleteModal();
    });

    // Borrado individual (global, la llama el onclick inline)
    function deleteOne(url) {
        openDeleteModal(@json(__('messages.students_delete_confirm')), () => {
            const f = document.getElementById('deleteOneForm');
            f.action = url;
            f.submit();
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Menú tres puntos
        document.querySelectorAll('.js-row-menu-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = btn.closest('.row-menu');
                const open = menu.classList.contains('is-open');
                document.querySelectorAll('.row-menu.is-open').forEach(m => m.classList.remove('is-open'));
                if (!open) {
                    const rect = btn.getBoundingClientRect();
                    menu.classList.toggle('drop-up', (window.innerHeight - rect.bottom) < 220);
                    menu.classList.add('is-open');
                }
            });
        });
        window.addEventListener('click', () => {
            document.querySelectorAll('.row-menu.is-open').forEach(m => m.classList.remove('is-open'));
        });

        // Checkboxes
        const checkAll = document.getElementById('checkAll');
        const rowChecks = document.querySelectorAll('.js-row-check');
        const bulkBar = document.getElementById('bulkBar');
        const bulkCount = document.getElementById('bulkCount');

        function refreshBulk() {
            const seleccionados = document.querySelectorAll('.js-row-check:checked').length;
            bulkCount.textContent = seleccionados;
            bulkBar.classList.toggle('is-visible', seleccionados > 0);
            if (checkAll) {
                checkAll.checked = seleccionados > 0 && seleccionados === rowChecks.length;
                checkAll.indeterminate = seleccionados > 0 && seleccionados < rowChecks.length;
            }
        }

        checkAll?.addEventListener('change', () => {
            rowChecks.forEach(c => c.checked = checkAll.checked);
            refreshBulk();
        });
        rowChecks.forEach(c => c.addEventListener('change', refreshBulk));

        window.clearSelection = function() {
            rowChecks.forEach(c => c.checked = false);
            if (checkAll) { checkAll.checked = false; checkAll.indeterminate = false; }
            refreshBulk();
        };

        // Submit del borrado múltiple → abre modal
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const n = document.querySelectorAll('.js-row-check:checked').length;
            e.preventDefault();
            if (n === 0) return;
            const txt = @json(__('messages.confirm_delete_selected')).replace(':count', n);
            openDeleteModal(txt, () => this.submit());
        });
    });
    </script>
    @endpush
</x-app-layout>