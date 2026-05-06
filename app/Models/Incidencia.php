<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidencia extends Model
{
    protected $table = 'incidencies';

    public const TIPUS_ASSISTENCIA = 'assistencia';
    public const TIPUS_DEURES = 'deures';
    public const TIPUS_MATERIAL = 'material';
    public const TIPUS_AMONESTACIO = 'amonestacio';

    public const TIPUS_VALIDS = [
        self::TIPUS_ASSISTENCIA,
        self::TIPUS_DEURES,
        self::TIPUS_MATERIAL,
        self::TIPUS_AMONESTACIO,
    ];

    public const TIPUS_LABELS = [
        self::TIPUS_ASSISTENCIA => 'Assistència',
        self::TIPUS_DEURES => 'Deures',
        self::TIPUS_MATERIAL => 'Material',
        self::TIPUS_AMONESTACIO => 'Amonestació',
    ];

    public const TIPUS_ICONES = [
        self::TIPUS_ASSISTENCIA => 'bi-person-x',
        self::TIPUS_DEURES => 'bi-journal-x',
        self::TIPUS_MATERIAL => 'bi-bag-x',
        self::TIPUS_AMONESTACIO => 'bi-exclamation-triangle',
    ];

    protected $fillable = [
        'espai_id',
        'alumne_id',
        'grup_id',
        'aula_horari_id',
        'usuari_espai_id',
        'tipus',
        'data',
        'observacions',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function alumne(): BelongsTo
    {
        return $this->belongsTo(Alumne::class);
    }

    public function grup(): BelongsTo
    {
        return $this->belongsTo(Grup::class);
    }

    public function aulaHorari(): BelongsTo
    {
        return $this->belongsTo(AulaHorario::class, 'aula_horari_id');
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'usuari_espai_id');
    }
}