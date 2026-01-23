<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900">
            Crear Espai
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-2xl p-8 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-6">Introdueix la informació del nou espai</h3>

                <form method="POST" action="{{ route('espais.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="nom" class="block text-gray-700 font-medium mb-2">Nom</label>
                        <input id="nom" name="nom" type="text" value="{{ old('nom') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200"
                               placeholder="Nom de l'espai" autofocus>
                        @error('nom')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="descripcio" class="block text-gray-700 font-medium mb-2">Descripció (opcional)</label>
                        <textarea id="descripcio" name="descripcio" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200"
                                  placeholder="Escriu una descripció breu">{{ old('descripcio') }}</textarea>
                        @error('descripcio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 hover:shadow-lg transition duration-200">
                            Desar
                        </button>
                        <a href="{{ route('espais.index') }}"
                           class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-full hover:bg-gray-300 hover:shadow-md transition duration-200 text-center">
                            Cancel·lar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
