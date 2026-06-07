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
                ->with('status', __('messages.select_space_first'));
        }

        return Espai::findOrFail($espaiId);
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

        $totalAlumnes = $espai->alumnes()->count();
        $filtrats = $query->count();

        $alumnes = $query
            ->orderByRaw('LOWER(nom) ASC')
            ->paginate(10)
            ->withQueryString();

        return view('espai.alumnes.index', [
            'espai' => $espai,
            'alumnes' => $alumnes,
            'totalAlumnes' => $totalAlumnes,
            'filtrats' => $filtrats,
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

        $jaExisteix = $espai->alumnes()
            ->where('idalu', $data['idalu'])
            ->exists();

        if ($jaExisteix) {
            return back()
                ->withErrors(['idalu' => __('messages.idalu_exists')])
                ->withInput();
        }

        $espai->alumnes()->create($data);

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', __('messages.student_created'));
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
            ->with('ok', __('messages.student_deleted'));
    }

    public function edit(Request $request, Alumne $alumne)
    {
        $espai = $this->getEspai($request);

        if ((int) $alumne->espai_id !== (int) $espai->id) {
            abort(404);
        }

        return view('espai.alumnes.edit', [
            'espai' => $espai,
            'alumne' => $alumne,
        ]);
    }

    public function update(Request $request, Alumne $alumne)
    {
        $espai = $this->getEspai($request);

        if ((int) $alumne->espai_id !== (int) $espai->id) {
            abort(404);
        }

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'cognoms' => ['nullable', 'string', 'max:255'],
            'correu' => ['nullable', 'email', 'max:255'],
            'idalu' => ['required', 'string', 'size:11'],
            'telefon' => ['nullable', 'string', 'max:20'],
        ]);

        $idaluRepetit = $espai->alumnes()
            ->where('idalu', $data['idalu'])
            ->where('id', '!=', $alumne->id)
            ->exists();

        if ($idaluRepetit) {
            return back()
                ->withErrors(['idalu' => __('messages.idalu_exists')])
                ->withInput();
        }

        $alumne->update($data);

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', __('messages.student_updated'));
    }

   //CSV

    public function importForm(Request $request)
    {
        $espai = $this->getEspai($request);
        return view('espai.alumnes.import', compact('espai'));
    }

    public function import(Request $request)
    {

     if (auth()->user()->plan !== 'premium') {

        abort(403, __('messages.premium_feature'));
    }
        $espai = $this->getEspai($request);

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = fopen($request->file('csv')->getRealPath(), 'r');

        $header = fgetcsv($file);

        $normalized = [];
        foreach ($header as $h) {
            $normalized[] = strtolower(trim($h));
        }

        $map = [
            'nom'      => ['nom', 'nombre', 'name', 'first name'],
            'cognoms'  => ['cognoms', 'apellidos', 'surname', 'last name'],
            'correu'   => ['correu', 'correo', 'email', 'mail'],
            'idalu'    => ['idalu', 'id', 'identificador', 'student id'],
            'telefon'  => ['telefon', 'telefono', 'tel', 'phone'],
            'grup'     => ['grup', 'grupo', 'group', 'class'],
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

            $nom = '';
            if (isset($index['nom']) && isset($row[$index['nom']])) {
                $nom = $row[$index['nom']];
            }

            $cognoms = '';
            if (isset($index['cognoms']) && isset($row[$index['cognoms']])) {
                $cognoms = $row[$index['cognoms']];
            }

            $correu = null;
            if (isset($index['correu']) && isset($row[$index['correu']])) {
                $correu = $row[$index['correu']];
            }

            $idalu = null;
            if (isset($index['idalu']) && isset($row[$index['idalu']])) {
                $idalu = $row[$index['idalu']];
            }

            $telefon = null;
            if (isset($index['telefon']) && isset($row[$index['telefon']])) {
                $telefon = $row[$index['telefon']];
            }

            $alumne = $espai->alumnes()->create([
                'nom'      => $nom,
                'cognoms'  => $cognoms,
                'correu'   => $correu,
                'idalu'    => $idalu,
                'telefon'  => $telefon,
            ]);

            $grupNom = null;
            if (isset($index['grup']) && isset($row[$index['grup']])) {
                $grupNom = $row[$index['grup']];
            }

            if ($grupNom) {
                $grupNom = trim($grupNom);

                $grup = $espai->grups()->firstOrCreate(['nom' => $grupNom]);

                $alumne->grups()->attach($grup->id);
            }
        }

        fclose($file);

        return redirect()->route('espai.alumnes.index')
            ->with('ok', __('messages.students_imported'));
    }

    public function export(Request $request)
    {
        if (auth()->user()->plan !== 'premium') {

        abort(403, __('messages.premium_feature'));
    }
        $espai = $this->getEspai($request);

        $alumnes = $espai->alumnes()->with('grups')->get();

        $filename = 'alumnes_espai_' . $espai->id . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($alumnes) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['nom', 'cognoms', 'correu', 'idalu', 'telefon', 'grups']);

            foreach ($alumnes as $alumne) {

                $nomsGrups = [];
                foreach ($alumne->grups as $g) {
                    $nomsGrups[] = $g->nom;
                }
                $grups = implode(', ', $nomsGrups);

                fputcsv($output, [
                    $alumne->nom,
                    $alumne->cognoms,
                    $alumne->correu,
                    $alumne->idalu,
                    $alumne->telefon,
                    $grups,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function info(Alumne $alumne)
    {
        return view('espai.alumnes.info', compact('alumne'));
    }
}