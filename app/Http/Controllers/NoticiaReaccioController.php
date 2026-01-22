<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\NoticiaReaccio;
use Illuminate\Http\Request;

class NoticiaReaccioController extends Controller
{
    private const TIPUS = ['like', 'love', 'laugh', 'wow', 'sad'];

    public function store(Request $request, Noticia $noticia)
    {
        $espaiId = $request->session()->get('espai_id');
        $usuariEspaiId = $request->session()->get('usuari_espai_id');

        abort_unless($espaiId && $usuariEspaiId, 403);
        abort_unless((int)$noticia->espai_id === (int)$espaiId, 404);

        $data = $request->validate([
            'tipus' => ['required', 'in:' . implode(',', self::TIPUS)],
        ]);

        NoticiaReaccio::updateOrCreate(
            ['noticia_id' => $noticia->id, 'usuari_espai_id' => $usuariEspaiId],
            ['tipus' => $data['tipus']]
        );

        return back();
    }

    public function destroy(Request $request, Noticia $noticia)
    {
        $espaiId = $request->session()->get('espai_id');
        $usuariEspaiId = $request->session()->get('usuari_espai_id');

        abort_unless($espaiId && $usuariEspaiId, 403);
        abort_unless((int)$noticia->espai_id === (int)$espaiId, 404);

        NoticiaReaccio::where('noticia_id', $noticia->id)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->delete();

        return back();
    }
}

