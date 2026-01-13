<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar espai
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('espais.update', $espai) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="nom" class="block font-medium mb-1">Nom</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom', $espai->nom) }}"
                               class="w-full border rounded px-3 py-2" autofocus>
                        @error('nom')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="descripcio" class="block font-medium mb-1">Descripció (opcional)</label>
                        <textarea id="descripcio" name="descripcio" rows="4"
                                  class="w-full border rounded px-3 py-2">{{ old('descripcio', $espai->descripcio) }}</textarea>
                        @error('descripcio')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                                Guardar canvis
                            </button>
                            <a href="{{ route('espais.index') }}" class="px-4 py-2 bg-gray-200 rounded">
                                Tornar
                            </a>
                        </div>

                        <form method="POST" action="{{ route('espais.destroy', $espai) }}"
                              onsubmit="return confirm('Segur que vols eliminar aquest espai?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
