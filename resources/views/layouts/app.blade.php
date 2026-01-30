<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4">


    <title>{{ config('app.name', 'AulaDesk') }}</title>

    <!-- Fuente -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS principal -->
    @vite(['resources/css/app.css', 'resources/css/espaisIndex.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased">

    <div class="min-h-screen">

        @isset($header)
            <!-- HEADER SEMI-TRANSPARENTE -->
            <header class="bg-white/80 backdrop-blur shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- MODAL ELIMINAR NOTÍCIA -->
    <div id="deleteModal" class="modal-periodic">
        <div class="modal-periodic__content">
            <span class="modal-periodic__close">&times;</span>
            <div class="modal-periodic__icon">📰</div>
            <h3 class="modal-periodic__title">Eliminar notícia</h3>
            <p class="modal-periodic__text">
                Estàs segur que vols eliminar aquesta notícia? Aquesta acció no es pot desfer.
            </p>
            <div class="modal-periodic__actions">
                <button id="cancelDelete" class="btn btn-secondary">Cancelar</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS MODAL -->
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('deleteModal');
        const closeBtn = document.querySelector('.modal-periodic__close');
        const cancelBtn = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');

        function openModal(action) {
            deleteForm.setAttribute('action', action);
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', () => {
                const action = button.getAttribute('data-action');
                openModal(action);
            });
        });

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === "Escape") closeModal();
        });
    });
    </script>
</body>
</html>
