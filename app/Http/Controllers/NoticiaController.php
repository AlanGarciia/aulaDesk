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

    /**
     * Retorna l'usuari d'espai actiu (obligatori per crear/editar/eliminar).
     */
    private function usuariEspaiActiuId(Request $request): int
    {
        $id = $request->session()->get('usuari_espai_id');
        abort_unless($id, 403);

        return (int) $id;
    }

    /**
     * Només el creador pot modificar/eliminar.
     */
    private function assertCreador(Request $request, Noticia $noticia): void
    {
        $usuariEspaiId = $this->usuariEspaiActiuId($request);

        abort_unless((int) $noticia->usuari_espai_id === $usuariEspaiId, 403);
    }

    public function index(Request $request)
    {
        $espai = $this->espaiActiu($request);

        $tipusDisponibles = self::TIPUS;
        $tipusSeleccionat = $request->query('tipus');

        $noticiesQuery = Noticia::where('espai_id', $espai->id)
            ->withCount('reaccions')
            ->latest();

        if ($tipusSeleccionat && in_array($tipusSeleccionat, $tipusDisponibles, true)) {
            $noticiesQuery->where('tipus', $tipusSeleccionat);
        }

        $noticies = $noticiesQuery->get();

        return view('espai.noticies.index', compact(
            'espai',
            'noticies',
            'tipusDisponibles',
            'tipusSeleccionat'
        ));
    }

    public function create(Request $request)
    {
        // Manté el mateix control d'espai
        $this->espaiActiu($request);

        $tipusDisponibles = self::TIPUS;

        return view('espai.noticies.create', compact('tipusDisponibles'));
    }

    public function store(Request $request)
    {
        $espai = $this->espaiActiu($request);
        $usuariEspaiId = $this->usuariEspaiActiuId($request);

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
            'usuari_espai_id' => $usuariEspaiId,
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

    public function edit(Request $request, Noticia $noticia)
    {
        $espai = $this->espaiActiu($request);

        // Ha de ser de l'espai actiu
        abort_unless((int) $noticia->espai_id === (int) $espai->id, 404);

        // Només creador
        $this->assertCreador($request, $noticia);

        $tipusDisponibles = self::TIPUS;

        return view('espai.noticies.edit', compact('noticia', 'tipusDisponibles'));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $espai = $this->espaiActiu($request);

        abort_unless((int) $noticia->espai_id === (int) $espai->id, 404);
        $this->assertCreador($request, $noticia);

        $data = $request->validate([
            'titol' => ['required','string','max:255'],
            'contingut' => ['nullable','string'],
            'tipus' => ['required','in:' . implode(',', self::TIPUS)],
            // si vols permetre canviar imatge:
            'imatge' => ['nullable','image','max:2048'],
            // per eliminar imatge actual amb checkbox:
            'treure_imatge' => ['nullable','boolean'],
        ]);

        // Treure imatge actual
        if (!empty($data['treure_imatge']) && $noticia->imatge_path) {
            Storage::disk('public')->delete($noticia->imatge_path);
            $noticia->imatge_path = null;
        }

        // Canviar imatge (opcional)
        if ($request->hasFile('imatge')) {
            if ($noticia->imatge_path) {
                Storage::disk('public')->delete($noticia->imatge_path);
            }
            $noticia->imatge_path = $request->file('imatge')->store('noticies', 'public');
        }

        $noticia->titol = $data['titol'];
        $noticia->contingut = $data['contingut'] ?? null;
        $noticia->tipus = $data['tipus'];
        $noticia->save();

        return redirect()
            ->route('espai.noticies.index')
            ->with('status', 'Notícia actualitzada correctament.');
    }

    public function destroy(Request $request, Noticia $noticia)
    {
        $espai = $this->espaiActiu($request);

        abort_unless((int)$noticia->espai_id === (int)$espai->id, 404);

        // ✅ Només el creador pot eliminar
        $this->assertCreador($request, $noticia);

        if ($noticia->imatge_path) {
            Storage::disk('public')->delete($noticia->imatge_path);
        }

        $noticia->delete();

        return redirect()
            ->route('espai.noticies.index')
            ->with('status', 'Notícia eliminada.');
    }
}