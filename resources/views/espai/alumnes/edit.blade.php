@push('styles')
    @vite('resources/css/espai/alumnes/alumnesCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <div class="card">

                <h2 class="inside-title">{{ __('messages.students_edit_title') }}</h2>

                @if ($errors->any())
                    <div class="alert-danger">
                        {{ __('messages.form_has_errors') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('espai.alumnes.update', $alumne) }}">
                    @csrf
                    @method('PUT')

                    {{-- DADES DE L'ALUMNE --}}
                    <h3 class="section-title">{{ __('messages.student_data') }}</h3>

                    <div class="grid-2">
                        <div class="field">
                            <label for="nom" class="label">{{ __('messages.name') }}</label>
                            <input id="nom" name="nom" type="text" class="input"
                                   value="{{ old('nom', $alumne->nom) }}" required>
                            @error('nom') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="idalu" class="label">{{ __('messages.idalu_label') }}</label>
                            <input id="idalu" name="idalu" type="text" maxlength="11" class="input"
                                   value="{{ old('idalu', $alumne->idalu) }}" required>
                            @error('idalu') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="cognom1" class="label">{{ __('messages.surname1') }}</label>
                            <input id="cognom1" name="cognom1" type="text" class="input"
                                   value="{{ old('cognom1', $alumne->cognom1) }}">
                            @error('cognom1') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="cognom2" class="label">{{ __('messages.surname2') }}</label>
                            <input id="cognom2" name="cognom2" type="text" class="input"
                                   value="{{ old('cognom2', $alumne->cognom2) }}">
                            @error('cognom2') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="data_naixement" class="label">{{ __('messages.birth_date') }}</label>
                            <input id="data_naixement" name="data_naixement" type="date" class="input"
                                   value="{{ old('data_naixement', optional($alumne->data_naixement)->format('Y-m-d')) }}">
                            @error('data_naixement') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="dni" class="label">{{ __('messages.dni') }}</label>
                            <input id="dni" name="dni" type="text" class="input"
                                   value="{{ old('dni', $alumne->dni) }}">
                            @error('dni') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="correu" class="label">{{ __('messages.email') }}</label>
                            <input id="correu" name="correu" type="email" class="input"
                                   value="{{ old('correu', $alumne->correu) }}">
                            @error('correu') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="telefon" class="label">{{ __('messages.phone') }}</label>
                            <input id="telefon" name="telefon" type="text" class="input"
                                   value="{{ old('telefon', $alumne->telefon) }}">
                            @error('telefon') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- FAMÍLIA / TUTORS --}}
                    <h3 class="section-title">{{ __('messages.family_tutors') }}</h3>

                    <div id="tutorsWrap">
                        @php
                            // Si hay errores usamos old(); si no, los tutores guardados; y si no hay, una fila vacía
                            $tutorsData = old('tutors');
                            if ($tutorsData === null) {
                                $tutorsData = $alumne->tutors->map(fn($t) => [
                                    'parentiu' => $t->parentiu,
                                    'nom' => $t->nom,
                                    'cognoms' => $t->cognoms,
                                    'correu' => $t->correu,
                                    'telefon' => $t->telefon,
                                    'dni' => $t->dni,
                                ])->toArray();
                                if (empty($tutorsData)) $tutorsData = [[]];
                            }
                        @endphp

                        @foreach($tutorsData as $i => $t)
                            <div class="tutor-row" data-index="{{ $i }}">
                                <div class="tutor-row__head">
                                    <span class="tutor-row__title">{{ __('messages.tutor') }}</span>
                                    <button type="button" class="tutor-remove" title="{{ __('messages.delete') }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="grid-2">
                                    <div class="field">
                                        <label class="label">{{ __('messages.relationship') }}</label>
                                        <input name="tutors[{{ $i }}][parentiu]" type="text"
                                               value="{{ $t['parentiu'] ?? '' }}" class="input"
                                               placeholder="{{ __('messages.relationship_ph') }}">
                                    </div>
                                    <div class="field">
                                        <label class="label">{{ __('messages.name') }}</label>
                                        <input name="tutors[{{ $i }}][nom]" type="text"
                                               value="{{ $t['nom'] ?? '' }}" class="input">
                                    </div>
                                </div>

                                <div class="grid-2">
                                    <div class="field">
                                        <label class="label">{{ __('messages.surnames') }}</label>
                                        <input name="tutors[{{ $i }}][cognoms]" type="text"
                                               value="{{ $t['cognoms'] ?? '' }}" class="input">
                                    </div>
                                    <div class="field">
                                        <label class="label">{{ __('messages.dni') }}</label>
                                        <input name="tutors[{{ $i }}][dni]" type="text"
                                               value="{{ $t['dni'] ?? '' }}" class="input">
                                    </div>
                                </div>

                                <div class="grid-2">
                                    <div class="field">
                                        <label class="label">{{ __('messages.email') }}</label>
                                        <input name="tutors[{{ $i }}][correu]" type="email"
                                               value="{{ $t['correu'] ?? '' }}" class="input">
                                    </div>
                                    <div class="field">
                                        <label class="label">{{ __('messages.phone') }}</label>
                                        <input name="tutors[{{ $i }}][telefon]" type="text"
                                               value="{{ $t['telefon'] ?? '' }}" class="input">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="addTutor" class="btn btn-ghost">
                        <i class="bi bi-plus-lg"></i> {{ __('messages.add_tutor') }}
                    </button>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrap = document.getElementById('tutorsWrap');
        const addBtn = document.getElementById('addTutor');

        let idx = wrap.querySelectorAll('.tutor-row').length;

        function plantilla(i){
            return `
            <div class="tutor-row" data-index="${i}">
                <div class="tutor-row__head">
                    <span class="tutor-row__title">@json(__('messages.tutor'))</span>
                    <button type="button" class="tutor-remove" title="@json(__('messages.delete'))">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label class="label">@json(__('messages.relationship'))</label>
                        <input name="tutors[${i}][parentiu]" type="text" class="input" placeholder="@json(__('messages.relationship_ph'))">
                    </div>
                    <div class="field">
                        <label class="label">@json(__('messages.name'))</label>
                        <input name="tutors[${i}][nom]" type="text" class="input">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label class="label">@json(__('messages.surnames'))</label>
                        <input name="tutors[${i}][cognoms]" type="text" class="input">
                    </div>
                    <div class="field">
                        <label class="label">@json(__('messages.dni'))</label>
                        <input name="tutors[${i}][dni]" type="text" class="input">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label class="label">@json(__('messages.email'))</label>
                        <input name="tutors[${i}][correu]" type="email" class="input">
                    </div>
                    <div class="field">
                        <label class="label">@json(__('messages.phone'))</label>
                        <input name="tutors[${i}][telefon]" type="text" class="input">
                    </div>
                </div>
            </div>`;
        }

        addBtn.addEventListener('click', () => {
            wrap.insertAdjacentHTML('beforeend', plantilla(idx));
            idx++;
        });

        wrap.addEventListener('click', (e) => {
            const btn = e.target.closest('.tutor-remove');
            if (!btn) return;
            const rows = wrap.querySelectorAll('.tutor-row');
            if (rows.length > 1) {
                btn.closest('.tutor-row').remove();
            } else {
                btn.closest('.tutor-row').querySelectorAll('input').forEach(i => i.value = '');
            }
        });
    });
    </script>
    @endpush

</x-app-layout>