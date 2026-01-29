<?php

namespace App\Http\Controllers;

use App\Models\FranjaHoraria;
use Illuminate\Http\Request;

class FranjaHorariaController extends Controller
{
    private function currentEspaiId(): ?int
    {
        $espaiId = session('espai_id');
        if ($espaiId) {
            return (int) $espaiId;
        }

        $espai = session('espai');

        if (is_array($espai) && isset($espai['id'])) {
            return (int) $espai['id'];
        }

        if (is_object($espai) && isset($espai->id)) {
            return (int) $espai->id;
        }

        return null;
    }

    public function index()
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        $franges = FranjaHoraria::where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        return view('espai.franges.index', compact('franges'));
    }

    public function create()
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        return view('espai.franges.create');
    }

    public function store(Request $request)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        $data = $request->validate(
            [
                'ordre' => ['required', 'integer', 'min:1'],
                'inici' => ['required', 'date_format:H:i'],
                'fi'    => ['required', 'date_format:H:i'],
                'nom'   => ['nullable', 'string', 'max:50'],
            ],
            [],
            [
                'ordre' => 'ordre',
                'inici' => 'inici',
                'fi' => 'fi',
                'nom' => 'nom',
            ]
        );

        $exists = FranjaHoraria::where('espai_id', $espaiId)
            ->where('ordre', $data['ordre'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['ordre' => 'Ja existeix una franja amb aquest ordre.'])->withInput();
        }

        $data['espai_id'] = $espaiId;

        FranjaHoraria::create($data);

        return redirect()->route('espai.franges.index')->with('ok', 'Franja creada.');
    }

    public function edit(FranjaHoraria $franja)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if($franja->espai_id !== $espaiId, 403);

        return view('espai.franges.edit', compact('franja'));
    }

    public function update(Request $request, FranjaHoraria $franja)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if($franja->espai_id !== $espaiId, 403);

        $data = $request->validate(
            [
                'ordre' => ['required', 'integer', 'min:1'],
                'inici' => ['required', 'date_format:H:i'],
                'fi'    => ['required', 'date_format:H:i'],
                'nom'   => ['nullable', 'string', 'max:50'],
            ]
        );

        $exists = FranjaHoraria::where('espai_id', $espaiId)
            ->where('ordre', $data['ordre'])
            ->where('id', '!=', $franja->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['ordre' => 'Ja existeix una franja amb aquest ordre.'])->withInput();
        }

        $franja->update($data);

        return redirect()->route('espai.franges.index')->with('ok', 'Franja actualitzada.');
    }

    public function destroy(FranjaHoraria $franja)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if($franja->espai_id !== $espaiId, 403);

        $franja->delete();

        return redirect()->route('espai.franges.index')->with('ok', 'Franja eliminada.');
    }
}