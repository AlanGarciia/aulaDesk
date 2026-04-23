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
        Paginator::useBootstrapFive();
 
        Blade::if('canEspai', function ($permission) {
 
            $espaiUserId = session('usuari_espai_id');
 
            if (!$espaiUserId) {
                return false;
            }
 
            $espaiUser = UsuariEspai::find($espaiUserId);
 
            if (!$espaiUser) {
                return false;
            }
 
            return $espaiUser->canEspai($permission);
        });
 
        Blade::directive('cantEspaiClass', function ($expression) {
            return "<?php
                \$_espaiUserId = session('usuari_espai_id');
                \$_espaiUser   = \$_espaiUserId ? \\App\\Models\\UsuariEspai::find(\$_espaiUserId) : null;
                echo (\$_espaiUser && \$_espaiUser->canEspai({$expression})) ? '' : 'btn-disabled';
            ?>";
        });
    }
}