<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\UsuariEspai;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔥 Activar paginación con números (Bootstrap 5)
        Paginator::useBootstrapFive();

        /**
         * Helper Blade: @canEspai('permiso')
         * Comprueba permisos usando el usuario del espai guardado en sesión.
         */
        Blade::if('canEspai', function ($permission) {

            // ID del usuario interno del espai
            $espaiUserId = session('usuari_espai_id');

            if (!$espaiUserId) {
                return false;
            }

            // Recuperar el usuario del espai
            $espaiUser = UsuariEspai::find($espaiUserId);

            if (!$espaiUser) {
                return false;
            }

            // Comprobar permiso
            return $espaiUser->canEspai($permission);
        });
    }
}
