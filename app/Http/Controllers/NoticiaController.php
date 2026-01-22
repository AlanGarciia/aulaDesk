<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticiaController extends Controller
{
    private const TIPUS = ['noticia', 'avis', 'urgent', 'event'];

    private function espaiActiu(Request $request): Espai
    {
        $espaiId = $request->session()->get('espai_id');
        abort_unless($espaiId, 403);

        return Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $espai = $this->espaiActiu($request);

        $noticies = Noticia::where('espai_id', $espai->id)
            ->latest()
            ->withCount('reaccions')
            ->get();

        return view('espai.noticies.index', compact('espai', 'noticies'));
    }

    public function create(Request $request)
    {
        $espai = $this->espaiActiu($request);
        $tipus = self::TIPUS;

        return view('espai.noticies.create', compact('espai', 'tipus'));
    }

    public function store(Request $request)
    {
        $espai = $this->espaiActiu($request);

        $data = $request->validate([
            'titol' => ['required','string','max:255'],
            'contingut' => ['nullable','string'],
            'tipus' => ['required','in:' . implode(',', self::TIPUS)],
            'imatge' => ['nullable','image','max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('imatge')) {
            $path = $request->file('imatge')->store('noticies', 'public');
        }

        Noticia::create([
            'espai_id' => $espai->id,
            'usuari_espai_id' => $request->session()->get('usuari_espai_id'),
            'titol' => $data['titol'],
            'contingut' => $data['contingut'] ?? null,
            'tipus' => $data['tipus'],
            'imatge_path' => $path,
            'publicat_el' => now(),
        ]);

        return redirect()
            ->route('espai.noticies.index')
            ->with('status', 'Notícia creada correctament.');
    }

    public function destroy(Request $request, Noticia $noticia)
    {
        $espai = $this->espaiActiu($request);

        abort_unless((int)$noticia->espai_id === (int)$espai->id, 404);

        if ($noticia->imatge_path) {
            Storage::disk('public')->delete($noticia->imatge_path);
        }

        $noticia->delete();

        return redirect()
            ->route('espai.noticies.index')
            ->with('status', 'Notícia eliminada.');
    }
}
