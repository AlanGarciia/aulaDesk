@push('styles')
    @vite('resources/css/espai/alumnes/alumnesImport.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <a href="{{ route('espai.alumnes.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
            </a>

            <div class="card">

                <div class="card__head">
                    <div class="card__icon"><i class="bi bi-filetype-csv"></i></div>
                    <div>
                        <h2 class="inside-title">{{ __('messages.students_import_title') }}</h2>
                        <p class="card__sub">{{ __('messages.import_sub') }}</p>
                    </div>
                </div>

                @if (session('import_error'))
                    <div class="alert-error">
                        <i class="bi bi-exclamation-triangle"></i> {{ session('import_error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('espai.alumnes.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <label class="label">{{ __('messages.csv_file') }}</label>
                        <input type="file" name="csv" class="input-file" required accept=".csv">
                        @error('csv') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="format-box">
                        <div class="format-box__title">
                            <i class="bi bi-info-circle"></i> {{ __('messages.expected_format') }}
                        </div>
                        <pre class="format-box__code">nom,cognom1,cognom2,correu,idalu,telefon,dni,data_naixement,grup,tutor1_parentiu,tutor1_nom,tutor1_cognoms,tutor1_correu,tutor1_telefon,tutor1_dni,tutor2_parentiu,tutor2_nom,tutor2_cognoms,tutor2_correu,tutor2_telefon,tutor2_dni</pre>
                        <p class="format-box__note">{{ __('messages.import_note') }}</p>

                        <a href="{{ route('espai.alumnes.plantilla') }}" class="template-link">
                            <i class="bi bi-download"></i> {{ __('messages.download_template') }}
                        </a>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> {{ __('messages.import') }}
                        </button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>