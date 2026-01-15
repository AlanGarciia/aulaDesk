<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Alan</h1>
</body>
</html>
@push('styles')
    @vite('resources/css/espai/espaiIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Espai</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="actions">
                <a href="#" class="btn btn-primary">
                    Afegir usuaris
                </a>
            </div>

            <div class="card">
                <h3 class="space-name">Panell de l'espai</h3>
                <p class="space-desc">
                    Aquí aniran totes les opcions del teu espai (usuaris, assignatures, classes, etc.).
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
