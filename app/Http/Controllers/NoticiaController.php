<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use App\Models\Noticia;
use App\Models\GuardiaSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticiaController extends Controller
{
    // Tipos que el usuario puede CREAR manualmente
    private const TIPUS_CREABLES = ['noticia', 'avis', 'urgent', 'event'];

    // Tipos que se pueden FILTRAR en el index (incluye guardia)
    private const TIPUS_FILTRE = ['noticia', 'avis', 'urgent', 'event', 'guardia'];

    private function espaiActiu(Request $request): Espai
    {
        $espaiId = $request->session()->get('espai_id');
        abort_unless($espaiId, 403);

        return Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    private function usuariEspaiActiuId(Request $request): int
    {
        $id = $request->session()->get('usuari_espai_id');
        abort_unless($id, 403);

        return (int) $id;
    }

    private function assertCreador(Request $request, Noticia $noticia): void
    {
        $usuariEspaiId = $this->usuariEspaiActiuId($request);
        abort_unless((int) $noticia->usuari_espai_id === $usuariEspaiId, 403);
    }

    public function index(Request $request)
    {
        $espai = $this->espaiActiu($request);

        // Para la UI: tipos disponibles para filtrar (incluye guardia)
        $tipusDisponibles = self::TIPUS_FILTRE;

        $tipusSeleccionat = (string) $request->query('tipus', '');
        if ($tipusSeleccionat !== '' && !in_array($tipusSeleccionat, $tipusDisponibles, true)) {
            $tipusSeleccionat = '';
        }

        $noticiesQuery = Noticia::where('espai_id', $espai->id)
            ->withCount('reaccions')
            ->latest();

        if ($tipusSeleccionat !== '') {
            $noticiesQuery->where('tipus', $tipusSeleccionat);
        }

        $noticies = $noticiesQuery->get();

        // Mapa solicitud por noticia_id (para botón aceptar en la vista)
        $solByNoticiaId = [];

        // Solo tiene sentido si estamos viendo guardias o si quieres pintarlo siempre
        // (lo dejo siempre porque cuesta poco y simplifica la vista)
        $sols = GuardiaSolicitud::query()
            ->where('espai_id', $espai->id)
            ->whereNotNull('noticia_id')
            ->get();

        foreach ($sols as $s) {
            if ($s->noticia_id) {
                $solByNoticiaId[(int) $s->noticia_id] = $s;
            }
        }

        return view('espai.noticies.index', compact(
            'espai',
            'noticies',
            'tipusDisponibles',
            'tipusSeleccionat',
            'solByNoticiaId'
        ));
    }

    public function create(Request $request)
    {
        $this->espaiActiu($request);

        // Solo los creables manualmente (NO guardia)
        $tipus = self::TIPUS_CREABLES;

        return view('espai.noticies.create', compact('tipus'));
    }

    public function store(Request $request)
    {
        $espai = $this->espaiActiu($request);
        $usuariEspaiId = $this->usuariEspaiActiuId($request);

        $data = $request->validate([
            'titol' => ['required', 'string', 'max:255'],
            'contingut' => ['nullable', 'string'],
            'tipus' => ['required', 'in:' . implode(',', self::TIPUS_CREABLES)],
            'imatge' => ['nullable', 'image', 'max:2048'],
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

        abort_unless((int) $noticia->espai_id === (int) $espai->id, 404);
        $this->assertCreador($request, $noticia);

        // solo tipos creables
        $tipusDisponibles = self::TIPUS_CREABLES;

        return view('espai.noticies.edit', compact('noticia', 'tipusDisponibles'));
    }

    public function show(Request $request, Noticia $noticia)
    {
        $espai = $this->espaiActiu($request);

        abort_unless((int) $noticia->espai_id === (int) $espai->id, 404);
        $noticia->loadCount('reaccions');

        return view('espai.noticies.show', compact('espai', 'noticia'));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $espai = $this->espaiActiu($request);

        abort_unless((int) $noticia->espai_id === (int) $espai->id, 404);
        $this->assertCreador($request, $noticia);

        // Si es noticia de guardia, no permitas editarla como noticia normal
        abort_if((string) $noticia->tipus === 'guardia', 403, 'Aquesta notícia de guàrdia no es pot editar manualment.');

        $data = $request->validate([
            'titol' => ['required', 'string', 'max:255'],
            'contingut' => ['nullable', 'string'],
            'tipus' => ['required', 'in:' . implode(',', self::TIPUS_CREABLES)],
            'imatge' => ['nullable', 'image', 'max:2048'],
            'treure_imatge' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['treure_imatge']) && $noticia->imatge_path) {
            Storage::disk('public')->delete($noticia->imatge_path);
            $noticia->imatge_path = null;
        }

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

        abort_unless((int) $noticia->espai_id === (int) $espai->id, 404);
        $this->assertCreador($request, $noticia);

        // Si es guardia, no borrar desde aquí (que se gestione por guardias)
        abort_if((string) $noticia->tipus === 'guardia', 403, 'Aquesta notícia de guàrdia no es pot eliminar manualment.');

        if ($noticia->imatge_path) {
            Storage::disk('public')->delete($noticia->imatge_path);
        }

        $noticia->delete();

        return redirect()
            ->route('espai.noticies.index')
            ->with('status', 'Notícia eliminada.');
    }
}