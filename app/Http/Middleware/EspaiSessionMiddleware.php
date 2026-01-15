<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EspaiSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('espai_id')) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        return $next($request);
    }
}
