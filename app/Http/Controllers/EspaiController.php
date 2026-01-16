<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use Illuminate\Http\Request;
use App\Models\UsuariEspai;
use Illuminate\Support\Facades\Hash;


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

        $espai = $request->user()->espais()->create([
            'nom' => $data['nom'],
            'descripcio' => $data['descripcio'] ?? null,
        ]);

        $espai->usuaris()->create([
            'nom' => 'admin',
            'rol' => 'admin',
            'contrasenya' => Hash::make('admin'),
        ]);

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai creat correctament. Usuari per defecte: admin / admin');
    }

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

    public function entrarForm(Espai $espai)
    {
        return view('espais.entrar', [
            'espai' => $espai,
        ]);
    }

    public function entrar(Request $request, Espai $espai)
    {
        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'contrasenya' => ['required', 'string', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'contrasenya' => 'contrasenya',
            ]
        );

        $usuari = UsuariEspai::where('espai_id', $espai->id)
            ->where('nom', $data['nom'])
            ->first();

        if (!$usuari || !Hash::check($data['contrasenya'], $usuari->contrasenya)) {
            return back()
                ->withErrors(['nom' => 'Nom o contrasenya incorrectes.'])
                ->withInput();
        }

        // ✅ Sessió d'espai
        $request->session()->put('espai_id', $espai->id);
        $request->session()->put('usuari_espai_id', $usuari->id);
        $request->session()->put('rol_espai', $usuari->rol);

        return redirect()->route('espai.index');
    }


    public function accesForm(Espai $espai)
    {
        return view('espais.acces', [
            'espai' => $espai,
        ]);
    }

    public function acces(Request $request, Espai $espai)
    {
        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'contrasenya' => ['required', 'string', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'contrasenya' => 'contrasenya',
            ]
        );

        $usuari = UsuariEspai::where('espai_id', $espai->id)
            ->where('nom', $data['nom'])
            ->first();

        if (!$usuari || !Hash::check($data['contrasenya'], $usuari->contrasenya)) {
            return back()
                ->withErrors(['nom' => 'Nom o contrasenya incorrectes.'])
                ->withInput();
        }

        // Sessió d'espai + rol
        $request->session()->put('espai_id', $espai->id);
        $request->session()->put('usuari_espai_id', $usuari->id);
        $request->session()->put('rol_espai', $usuari->rol);

        return redirect()->route('espai.index');
    }




}