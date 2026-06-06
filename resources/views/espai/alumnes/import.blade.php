@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <div class="card">

                <h2 class="inside-title">{{ __('messages.students_import_title') }}</h2>

                <form method="POST" action="{{ route('espai.alumnes.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <label class="label">{{ __('messages.csv_file') }}</label>
                        <input type="file" name="csv" class="input" required accept=".csv">
                    </div>

                    <p>{{ __('messages.expected_format') }}</p>
                    <pre>{{ __('messages.students_csv_format') }}</pre>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">{{ __('messages.import') }}</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>