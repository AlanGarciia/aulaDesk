<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use Illuminate\Http\Request;

class EspaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $espais = $request->user()->espais()->latest()->get();

        return view('espais.index', [
            'espais' => $espais,]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('espais.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'descripcio' => ['nullable', 'string'],
            ],
            [],
            [
                'nom' => 'nom',
                'descripcio' => 'descripció',
            ]
        );

        $request->user()->espais()->create([
            'nom' => $data['nom'],
            'descripcio' => $data['descripcio'] ?? null,
        ]);

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai creat correctament.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Espai $espai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Espai $espai)
    {
        if ($espai->user_id !== $request->user()->id) {
            abort(404);
        }

        return view('espais.edit', ['espai' => $espai]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Espai $espai)
    {
        if ($espai->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'descripcio' => ['nullable', 'string'],
            ],
            [],
            [
                'nom' => 'nom',
                'descripcio' => 'descripció',
            ]
        );

        $espai->update([
            'nom' => $data['nom'],
            'descripcio' => $data['descripcio'] ?? null,
        ]);

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai actualitzat correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Espai $espai)
    {
        if ($espai->user_id !== $request->user()->id) {
            abort(404);
        }

        $espai->delete();

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai eliminat correctament.');
    }

    public function entrar(Request $request, Espai $espai)
    {
        if ($espai->user_id !== $request->user()->id) {
            abort(404);
        }

        // Guardem l'espai actiu a la sessió
        $request->session()->put('espai_id', $espai->id);

        return redirect()->route('espai.index');
    }

}