<?php

namespace App\Http\Controllers;

use App\Models\Alumne;
use App\Models\Espai;
use Illuminate\Http\Request;

class AlumneController extends Controller
{
    private function getEspai(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        return Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $espai = $this->getEspai($request);

        $query = $espai->alumnes();

        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        if ($request->filled('idalu')) {
            $query->where('idalu', 'like', '%' . $request->idalu . '%');
        }

        $alumnes = $query
            ->orderByRaw('LOWER(nom) ASC')
            ->paginate(20);

        return view('espai.alumnes.index', [
            'espai' => $espai,
            'alumnes' => $alumnes,
        ]);
    }

    public function create(Request $request)
    {
        $espai = $this->getEspai($request);

        return view('espai.alumnes.create', [
            'espai' => $espai,
        ]);
    }

    public function store(Request $request)
    {
        $espai = $this->getEspai($request);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'cognoms' => ['nullable', 'string', 'max:255'],
            'correu' => ['nullable', 'email', 'max:255'],
            'idalu' => ['required', 'string', 'size:11'],
            'telefon' => ['nullable', 'string', 'max:20'],
        ]);

        if ($espai->alumnes()->where('idalu', $data['idalu'])->exists()) {
            return back()
                ->withErrors(['idalu' => 'Aquest IDALU ja existeix dins d’aquest espai.'])
                ->withInput();
        }

        $espai->alumnes()->create($data);

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', 'Alumne creat correctament.');
    }

    public function destroy(Request $request, Alumne $alumne)
    {
        $espai = $this->getEspai($request);

        if ((int) $alumne->espai_id !== (int) $espai->id) {
            abort(404);
        }

        $alumne->delete();

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', 'Alumne eliminat correctament.');
    }

    

    public function importForm(Request $request)
    {
        $espai = $this->getEspai($request);
        return view('espai.alumnes.import', compact('espai'));
    }

    public function import(Request $request)
    {
        $espai = $this->getEspai($request);

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = fopen($request->file('csv')->getRealPath(), 'r');

        $header = fgetcsv($file);

        $normalized = array_map(fn($h) => strtolower(trim($h)), $header);

        $map = [
            'nom'      => ['nom', 'nombre', 'name', 'first name'],
            'cognoms'  => ['cognoms', 'apellidos', 'surname', 'last name'],
            'correu'   => ['correu', 'correo', 'email', 'mail'],
            'idalu'    => ['idalu', 'id', 'identificador', 'student id'],
            'telefon'  => ['telefon', 'telefono', 'tel', 'phone'],
        ];

        $index = [];

        foreach ($map as $campo => $posibles) {
            foreach ($posibles as $nombre) {
                $col = array_search($nombre, $normalized);
                if ($col !== false) {
                    $index[$campo] = $col;
                    break;
                }
            }
        }

        while ($row = fgetcsv($file)) {

            $espai->alumnes()->create([
                'nom'      => $row[$index['nom']]      ?? '',
                'cognoms'  => $row[$index['cognoms']]  ?? '',
                'correu'   => $row[$index['correu']]   ?? null,
                'idalu'    => $row[$index['idalu']]    ?? null,
                'telefon'  => $row[$index['telefon']]  ?? null,
            ]);
        }

        fclose($file);

        return redirect()->route('espai.alumnes.index')
            ->with('ok', 'Alumnes importats correctament.');
    }

    public function export(Request $request)
    {
        $espai = $this->getEspai($request);

        $alumnes = $espai->alumnes()->get([
            'nom',
            'cognoms',
            'correu',
            'idalu',
            'telefon'
        ]);

        $filename = 'alumnes_espai_' . $espai->id . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($alumnes) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['nom', 'cognoms', 'correu', 'idalu', 'telefon']);

            foreach ($alumnes as $alumne) {
                fputcsv($output, [
                    $alumne->nom,
                    $alumne->cognoms,
                    $alumne->correu,
                    $alumne->idalu,
                    $alumne->telefon,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
