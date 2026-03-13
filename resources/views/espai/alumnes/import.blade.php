@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            <div class="card">

                <h2 class="inside-title">Importar alumnes des de CSV</h2>

                <form method="POST" action="{{ route('espai.alumnes.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <label class="label">Arxiu CSV</label>
                        <input type="file" name="csv" class="input" required accept=".csv">
                    </div>

                    <p>Format esperat:</p>
                    <pre>nom,cognoms,correu,idalu,telefon</pre>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Importar</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">Cancel·lar</a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
