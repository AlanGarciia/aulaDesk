<?php

namespace App\Http\Controllers;

use App\Models\Alumne;
use App\Models\Espai;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Nom (busca en nom, cognom1 i cognom2)
        if ($request->filled('nom')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->nom . '%')
                  ->orWhere('cognom1', 'like', '%' . $request->nom . '%')
                  ->orWhere('cognom2', 'like', '%' . $request->nom . '%');
            });
        }

        // Cognoms (només en cognom1 i cognom2)
        if ($request->filled('cognoms')) {
            $query->where(function ($q) use ($request) {
                $q->where('cognom1', 'like', '%' . $request->cognoms . '%')
                  ->orWhere('cognom2', 'like', '%' . $request->cognoms . '%');
            });
        }

        // IDALU
        if ($request->filled('idalu')) {
            $query->where('idalu', 'like', '%' . $request->idalu . '%');
        }

        // Telèfon
        if ($request->filled('telefon')) {
            $query->where('telefon', 'like', '%' . $request->telefon . '%');
        }

        // Grup
        if ($request->filled('grup')) {
            $grupId = $request->grup;
            $query->whereHas('grups', function ($q) use ($grupId) {
                $q->where('grups.id', $grupId);
            });
        }

        $totalAlumnes = $espai->alumnes()->count();
        $filtrats = $query->count();

        // Ordenar según el formato del espacio
        if (($espai->format_nom ?? 'nom_cognoms') === 'cognoms_nom') {
            $query->orderByRaw('LOWER(cognom1) ASC')
                  ->orderByRaw('LOWER(cognom2) ASC')
                  ->orderByRaw('LOWER(nom) ASC');
        } else {
            $query->orderByRaw('LOWER(nom) ASC');
        }

        $alumnes = $query->paginate(10)->withQueryString();

        // Llista de grups per al filtre
        $grups = $espai->grups()->orderBy('nom')->get();

        return view('espai.alumnes.index', [
            'espai' => $espai,
            'alumnes' => $alumnes,
            'totalAlumnes' => $totalAlumnes,
            'filtrats' => $filtrats,
            'grups' => $grups,
        ]);
    }

    /** Canviar el format de nom de l'espai (Nom Cognoms / Cognoms, Nom) */
    public function updateFormat(Request $request)
    {
        $espai = $this->getEspai($request);

        $data = $request->validate([
            'format_nom' => ['required', 'in:nom_cognoms,cognoms_nom'],
        ]);

        $espai->update(['format_nom' => $data['format_nom']]);

        return redirect()->route('espai.alumnes.index')
            ->with('ok', __('messages.format_updated'));
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
            'cognom1' => ['required', 'string', 'max:255'],
            'cognom2' => ['nullable', 'string', 'max:255'],
            'correu' => ['nullable', 'email', 'max:255'],
            'idalu' => ['required', 'string', 'size:11'],
            'telefon' => ['nullable', 'string', 'max:20'],
            'dni' => ['required', 'string', 'max:20'],
            'data_naixement' => ['required', 'date'],
            'tutors' => ['nullable', 'array'],
            'tutors.*.parentiu' => ['nullable', 'string', 'max:50'],
            'tutors.*.nom' => ['nullable', 'string', 'max:255'],
            'tutors.*.cognoms' => ['nullable', 'string', 'max:255'],
            'tutors.*.correu' => ['nullable', 'email', 'max:255'],
            'tutors.*.telefon' => ['nullable', 'string', 'max:20'],
            'tutors.*.dni' => ['nullable', 'string', 'max:20'],
        ]);

        $jaExisteix = $espai->alumnes()
            ->where('idalu', $data['idalu'])
            ->exists();

        if ($jaExisteix) {
            return back()
                ->withErrors(['idalu' => __('messages.idalu_exists')])
                ->withInput();
        }

        $slug = Alumne::generarSlug($espai->id, $data['nom'], $data['cognom1'] ?? '', $data['cognom2'] ?? null);

        $alumne = $espai->alumnes()->create([
            'nom' => $data['nom'],
            'cognom1' => $data['cognom1'] ?? null,
            'cognom2' => $data['cognom2'] ?? null,
            'slug' => $slug,
            'correu' => $data['correu'] ?? null,
            'idalu' => $data['idalu'],
            'telefon' => $data['telefon'] ?? null,
            'dni' => $data['dni'] ?? null,
            'data_naixement' => $data['data_naixement'] ?? null,
        ]);

        $this->syncTutors($alumne, $data['tutors'] ?? []);

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

        $alumne->load('tutors');

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
            'cognom1' => ['required', 'string', 'max:255'],
            'cognom2' => ['nullable', 'string', 'max:255'],
            'correu' => ['nullable', 'email', 'max:255'],
            'idalu' => ['required', 'string', 'size:11'],
            'telefon' => ['nullable', 'string', 'max:20'],
            'dni' => ['required', 'string', 'max:20'],
            'data_naixement' => ['required', 'date'],
            'tutors' => ['nullable', 'array'],
            'tutors.*.parentiu' => ['nullable', 'string', 'max:50'],
            'tutors.*.nom' => ['nullable', 'string', 'max:255'],
            'tutors.*.cognoms' => ['nullable', 'string', 'max:255'],
            'tutors.*.correu' => ['nullable', 'email', 'max:255'],
            'tutors.*.telefon' => ['nullable', 'string', 'max:20'],
            'tutors.*.dni' => ['nullable', 'string', 'max:20'],
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

        $slug = Alumne::generarSlug($espai->id, $data['nom'], $data['cognom1'] ?? '', $data['cognom2'] ?? null, $alumne->id);

        $alumne->update([
            'nom' => $data['nom'],
            'cognom1' => $data['cognom1'] ?? null,
            'cognom2' => $data['cognom2'] ?? null,
            'slug' => $slug,
            'correu' => $data['correu'] ?? null,
            'idalu' => $data['idalu'],
            'telefon' => $data['telefon'] ?? null,
            'dni' => $data['dni'] ?? null,
            'data_naixement' => $data['data_naixement'] ?? null,
        ]);

        $this->syncTutors($alumne, $data['tutors'] ?? []);

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', __('messages.student_updated'));
    }

    /** Borra los tutores actuales y recrea con los enviados (solo los que tengan nombre) */
    private function syncTutors(Alumne $alumne, array $tutors): void
    {
        $alumne->tutors()->delete();

        foreach ($tutors as $t) {
            $nom = trim($t['nom'] ?? '');
            if ($nom === '') continue; // ignora filas vacías

            $alumne->tutors()->create([
                'parentiu' => $t['parentiu'] ?? null,
                'nom' => $nom,
                'cognoms' => $t['cognoms'] ?? null,
                'correu' => $t['correu'] ?? null,
                'telefon' => $t['telefon'] ?? null,
                'dni' => $t['dni'] ?? null,
            ]);
        }
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
            'cognom1'  => ['cognom1', 'cognoms', 'apellido1', 'apellidos', 'surname', 'last name'],
            'cognom2'  => ['cognom2', 'apellido2', 'second surname'],
            'correu'   => ['correu', 'correo', 'email', 'mail'],
            'idalu'    => ['idalu', 'id', 'identificador', 'student id'],
            'telefon'  => ['telefon', 'telefono', 'tel', 'phone'],
            'dni'      => ['dni', 'nif', 'document'],
            'data_naixement' => ['data_naixement', 'fecha_nacimiento', 'naixement', 'nacimiento', 'birth', 'birthdate'],
            'grup'     => ['grup', 'grupo', 'group', 'class'],

            'tutor1_parentiu' => ['tutor1_parentiu', 'tutor1_parentesco'],
            'tutor1_nom'      => ['tutor1_nom', 'tutor1_nombre'],
            'tutor1_cognoms'  => ['tutor1_cognoms', 'tutor1_apellidos'],
            'tutor1_correu'   => ['tutor1_correu', 'tutor1_correo', 'tutor1_email'],
            'tutor1_telefon'  => ['tutor1_telefon', 'tutor1_telefono', 'tutor1_tel'],
            'tutor1_dni'      => ['tutor1_dni', 'tutor1_nif'],
            'tutor2_parentiu' => ['tutor2_parentiu', 'tutor2_parentesco'],
            'tutor2_nom'      => ['tutor2_nom', 'tutor2_nombre'],
            'tutor2_cognoms'  => ['tutor2_cognoms', 'tutor2_apellidos'],
            'tutor2_correu'   => ['tutor2_correu', 'tutor2_correo', 'tutor2_email'],
            'tutor2_telefon'  => ['tutor2_telefon', 'tutor2_telefono', 'tutor2_tel'],
            'tutor2_dni'      => ['tutor2_dni', 'tutor2_nif'],
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

        // Comprovar columnes obligatòries
        if (!isset($index['nom']) || !isset($index['idalu'])) {
            fclose($file);
            return back()->with('import_error', __('messages.import_missing_columns'));
        }

        $importats = 0;
        $ignorats = 0;
        $idalusVistos = [];

        while ($row = fgetcsv($file)) {

            $get = function ($campo) use ($index, $row) {
                if (isset($index[$campo]) && isset($row[$index[$campo]])) {
                    return $row[$index[$campo]];
                }
                return null;
            };

            $idalu   = trim((string) $get('idalu'));
            $nom     = $get('nom');
            $cognom1 = $get('cognom1');
            $dni     = $get('dni');

            // Fecha de nacimiento (acepta varios formatos, null si viene mal)
            $dataNaix = null;
            $rawData = $get('data_naixement');
            if ($rawData) {
                try {
                    $dataNaix = \Carbon\Carbon::parse(trim($rawData))->format('Y-m-d');
                } catch (\Exception $e) {
                    $dataNaix = null;
                }
            }

            // Camps obligatoris: nom, cognom1, idalu, dni, data_naixement
            $faltaObligatori = !trim((string) $nom)
                || !trim((string) $cognom1)
                || !trim((string) $idalu)
                || !trim((string) $dni)
                || !$dataNaix;

            // Saltar si falta algun obligatori, si ja s'ha vist en aquest CSV, o si ja existeix a la BD
            if ($faltaObligatori
                || in_array($idalu, $idalusVistos, true)
                || $espai->alumnes()->where('idalu', $idalu)->exists()) {
                $ignorats++;
                continue;
            }

            $idalusVistos[] = $idalu;

            // Crear l'alumne (amb slug)
            $slug = Alumne::generarSlug($espai->id, trim($nom), trim($cognom1), $get('cognom2'));

            $alumne = $espai->alumnes()->create([
                'nom'      => trim($nom),
                'cognom1'  => trim($cognom1),
                'cognom2'  => $get('cognom2'),
                'slug'     => $slug,
                'correu'   => $get('correu'),
                'idalu'    => $idalu,
                'telefon'  => $get('telefon'),
                'dni'      => trim($dni),
                'data_naixement' => $dataNaix,
            ]);

            $importats++;

            // Tutors (màxim 2, opcionals). Es crea cada un que tingui nom.
            foreach ([1, 2] as $n) {
                $tutorNom = $get("tutor{$n}_nom");
                if (!$tutorNom || trim($tutorNom) === '') continue;

                $alumne->tutors()->create([
                    'parentiu' => $get("tutor{$n}_parentiu"),
                    'nom'      => trim($tutorNom),
                    'cognoms'  => $get("tutor{$n}_cognoms"),
                    'correu'   => $get("tutor{$n}_correu"),
                    'telefon'  => $get("tutor{$n}_telefon"),
                    'dni'      => $get("tutor{$n}_dni"),
                ]);
            }

            // Grup (es crea si no existeix)
            $grupNom = $get('grup');
            if ($grupNom) {
                $grupNom = trim($grupNom);
                $grup = $espai->grups()->firstOrCreate(['nom' => $grupNom]);
                $alumne->grups()->attach($grup->id);
            }
        }

        fclose($file);

        return redirect()->route('espai.alumnes.index')
            ->with('ok', __('messages.import_result', ['imported' => $importats, 'ignored' => $ignorats]));
    }

    public function export(Request $request)
    {
        if (auth()->user()->plan !== 'premium') {
            abort(403, __('messages.premium_feature'));
        }
        $espai = $this->getEspai($request);

        $alumnes = $espai->alumnes()->with(['grups', 'tutors'])->get();

        $filename = 'alumnes_espai_' . $espai->id . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($alumnes) {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'nom', 'cognom1', 'cognom2', 'correu', 'idalu', 'telefon', 'dni', 'data_naixement', 'grup',
                'tutor1_parentiu', 'tutor1_nom', 'tutor1_cognoms', 'tutor1_correu', 'tutor1_telefon', 'tutor1_dni',
                'tutor2_parentiu', 'tutor2_nom', 'tutor2_cognoms', 'tutor2_correu', 'tutor2_telefon', 'tutor2_dni',
            ]);

            foreach ($alumnes as $alumne) {

                $nomsGrups = [];
                foreach ($alumne->grups as $g) {
                    $nomsGrups[] = $g->nom;
                }
                $grups = implode(', ', $nomsGrups);

                $t1 = $alumne->tutors->get(0);
                $t2 = $alumne->tutors->get(1);

                fputcsv($output, [
                    $alumne->nom,
                    $alumne->cognom1,
                    $alumne->cognom2,
                    $alumne->correu,
                    $alumne->idalu,
                    $alumne->telefon,
                    $alumne->dni,
                    $alumne->data_naixement ? $alumne->data_naixement->format('Y-m-d') : '',
                    $grups,
                    // tutor 1
                    $t1->parentiu ?? '',
                    $t1->nom ?? '',
                    $t1->cognoms ?? '',
                    $t1->correu ?? '',
                    $t1->telefon ?? '',
                    $t1->dni ?? '',
                    // tutor 2
                    $t2->parentiu ?? '',
                    $t2->nom ?? '',
                    $t2->cognoms ?? '',
                    $t2->correu ?? '',
                    $t2->telefon ?? '',
                    $t2->dni ?? '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function plantilla()
    {
        $filename = 'plantilla_alumnes.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $output = fopen('php://output', 'w');

            // Capçalera
            fputcsv($output, [
                'nom', 'cognom1', 'cognom2', 'correu', 'idalu', 'telefon', 'dni', 'data_naixement', 'grup',
                'tutor1_parentiu', 'tutor1_nom', 'tutor1_cognoms', 'tutor1_correu', 'tutor1_telefon', 'tutor1_dni',
                'tutor2_parentiu', 'tutor2_nom', 'tutor2_cognoms', 'tutor2_correu', 'tutor2_telefon', 'tutor2_dni',
            ]);

            // Fila d'exemple
            fputcsv($output, [
                'Alan', 'García', 'Pérez', 'alan@exemple.com', '10000000001', '600111222', '12345678A', '2010-03-14', '1r ESO A',
                'Pare', 'Josep', 'García Llull', 'josep@exemple.com', '600999001', '11111111A',
                'Mare', 'Anna', 'Pérez Mas', 'anna@exemple.com', '600999002', '22222222B',
            ]);

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function info(Request $request, Alumne $alumne)
    {
        $espai = $this->getEspai($request);

        if ((int) $alumne->espai_id !== (int) $espai->id) {
            abort(404);
        }

        $alumne->load('tutors');
        return view('espai.alumnes.info', compact('alumne'));
    }

    public function destroyMultiple(Request $request)
    {
        $espai = $this->getEspai($request);

        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        // Només esborra alumnes que pertanyen a aquest espai
        $eliminats = $espai->alumnes()
            ->whereIn('id', $data['ids'])
            ->get();

        $count = $eliminats->count();

        foreach ($eliminats as $alumne) {
            $alumne->delete(); // els tutors s'esborren en cascada
        }

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', __('messages.students_deleted_multiple', ['count' => $count]));
    }

    public function pdf(Request $request, Alumne $alumne)
    {
        $espai = $this->getEspai($request);

        if ((int) $alumne->espai_id !== (int) $espai->id) {
            abort(404);
        }

        $alumne->load('tutors', 'grups');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('espai.alumnes.pdf', [
            'alumne' => $alumne,
            'espai'  => $espai,
        ]);

        $nomFitxer = __('messages.file_prefix') . '_' . ($alumne->slug ?? $alumne->id) . '.pdf';

        return $pdf->download($nomFitxer);
    }

    
}