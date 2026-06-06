<x-app-layout>

    @push('styles')
    <style>
        :root{
            --night-0: #0f1729;
            --night-1: #0b1120;
            --panel: #1a2236;
            --panel-2: #151c2e;
            --line: rgba(255,255,255,.08);
            --text: #e7ebf3;
            --muted: #9aa6bd;
            --muted2: #6b7689;
            --blue: #3b82f6;
            --blue-deep: #2563eb;
            --danger: #ef4444;
        }

        .profile-page{
            min-height: 100vh;
            background: linear-gradient(180deg, var(--night-0), var(--night-1));
            color: var(--text);
            padding: 30px 0 70px;
            position: relative;
        }
        .profile-page::before{
            content:"";
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none; z-index: 0;
        }

        .profile-container{
            max-width: 640px;
            margin: 0 auto;
            padding: 0 18px;
            position: relative;
            z-index: 1;
        }

        /* Cabecera */
        .profile-header{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .profile-header h1{
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
        }
        .profile-back{
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--panel);
            border: 1px solid var(--line);
            color: var(--text);
            padding: 9px 16px;
            border-radius: 11px;
            text-decoration: none;
            font-weight: 600; font-size: .9rem;
            transition: transform .15s, background .15s, border-color .15s;
        }
        .profile-back:hover{ transform: translateX(-2px); background: #202a42; border-color: rgba(255,255,255,.14); }

        /* Cada sección (los partials) en tarjeta */
        .profile-section{
            position: relative;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 28px 26px;
            margin-bottom: 22px;
            overflow: hidden;
            box-shadow: 0 14px 40px rgba(0,0,0,.28);
        }
        .profile-section::before{
            content:""; position: absolute; top: 0; left: 0; right: 0;
            height: 4px; background: var(--blue);
        }
        .profile-section--danger::before{ background: var(--danger); }

        /* Sobrescribir los estilos Tailwind de los partials de Breeze */
        .profile-section h2{
            color: #fff !important;
            font-family: inherit;
        }
        .profile-section p{
            color: var(--muted) !important;
        }
        .profile-section label{
            color: var(--text) !important;
        }
        .profile-section input{
            background: var(--panel-2) !important;
            border: 1.5px solid var(--line) !important;
            color: var(--text) !important;
            border-radius: 12px !important;
            padding: .75rem .9rem !important;
        }
        .profile-section input:focus{
            border-color: var(--blue) !important;
            box-shadow: 0 0 0 4px rgba(59,130,246,.18) !important;
            outline: none !important;
        }

        /* Botones de Breeze dentro del perfil */
        .profile-section button[type="submit"],
        .profile-section .inline-flex{
            border-radius: 11px !important;
        }
    </style>
    @endpush

    <div class="profile-page">
        <div class="profile-container">

            <div class="profile-header">
                <h1>{{ __('messages.Profile') }}</h1>
                <a href="{{ route('espais.index') }}" class="profile-back">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>

            {{-- Información de perfil --}}
            <div class="profile-section">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Actualizar contraseña --}}
            <div class="profile-section">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Eliminar cuenta --}}
            <div class="profile-section profile-section--danger">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>