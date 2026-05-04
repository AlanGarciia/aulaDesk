<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UsuariEspai;

class CanEspai
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, $permission)
    {
        $espaiUserId = session('usuari_espai_id');

        if (!$espaiUserId) {
            abort(403, 'No hi ha cap usuari d\'espai actiu.');
        }

        $espaiUser = UsuariEspai::find($espaiUserId);

        if (!$espaiUser) {
            abort(403, 'Usuari d\'espai no trobat.');
        }

        if (!$espaiUser->canEspai($permission)) {
            abort(403, 'No tens permís per accedir aquí.');
        }

        return $next($request);
    }
}