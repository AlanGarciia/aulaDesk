<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\GuardiaSolicitud;
use App\Models\AulaHorario;

class RevertirGuardiesCaducades extends Command
{
    protected $signature = 'guardies:revertir-caducades';
    protected $description = 'Reverteix guardies acceptades quan han passat 7 dies (revertir_el).';

    public function handle(): int
    {
        $now = now();

        $sols = GuardiaSolicitud::query()
            ->where('estat', 'acceptada')
            ->whereNotNull('revertir_el')
            ->whereNull('revertit_el')
            ->where('revertir_el', '<=', $now)
            ->get();

        $count = 0;

        DB::transaction(function () use ($sols, &$count) {
            foreach ($sols as $sol) {
                $aulaId = (int) $sol->aula_id;
                $dia = (int) $sol->dia_setmana;
                $franjaId = (int) $sol->franja_horaria_id;

                $originalId = $sol->original_usuari_espai_id ? (int) $sol->original_usuari_espai_id : null;

                if ($originalId) {
                    AulaHorario::updateOrCreate(
                        [
                            'aula_id' => $aulaId,
                            'dia_setmana' => $dia,
                            'franja_horaria_id' => $franjaId,
                        ],
                        [
                            'usuari_espai_id' => $originalId,
                        ]
                    );
                }

                $sol->estat = 'finalitzada';
                $sol->revertit_el = now();
                $sol->save();

                $count++;
            }
        });

        $this->info("Guardies revertides: {$count}");
        return Command::SUCCESS;
    }
}
