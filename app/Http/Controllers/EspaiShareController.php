<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use App\Models\User;
use App\Models\UsuariExternEspai;
use Illuminate\Http\Request;

class EspaiShareController extends Controller
{
    public function store(Request $request, Espai $espai)
    {
        // solo el propietari pot compartir
        if ((int) $espai->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $usuari = User::where('email', $request->email)->first();

        if (!$usuari) {
            return back()->with('status', 'No existeix cap usuari amb aquest email.');
        }

        if ((int) $usuari->id === (int) $espai->user_id) {
            return back()->with('status', 'Aquest espai ja és teu.');
        }

        $ja = UsuariExternEspai::where('espai_id', $espai->id)
            ->where('user_id', $usuari->id)
            ->exists();

        if ($ja) {
            return back()->with('status', 'Aquest usuari ja té accés a l’espai.');
        }

        UsuariExternEspai::create([
            'espai_id' => $espai->id,
            'user_id' => $usuari->id,
        ]);

        return back()->with('status', 'Espai compartit correctament.');
    }
}