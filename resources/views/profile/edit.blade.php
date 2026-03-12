<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="profile-wrapper max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Información de perfil --}}
            @include('profile.partials.update-profile-information-form')

            {{-- Actualizar contraseña --}}
            @include('profile.partials.update-password-form')

            {{-- Eliminar cuenta --}}
            @include('profile.partials.delete-user-form')

        </div>
    </div>
</x-app-layout>
