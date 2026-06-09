<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Alumne extends Model
{
    protected $table = 'alumnes';

    protected $fillable = [
        'espai_id',
        'nom',
        'cognom1',
        'cognom2',
        'slug',
        'correu',
        'idalu',
        'telefon',
        'dni',
        'data_naixement',
    ];

    protected $casts = [
        'data_naixement' => 'date',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class);
    }

    public function aulas(): BelongsToMany
    {
        return $this->belongsToMany(Aula::class, 'aula_alumne');
    }

    public function grups(): BelongsToMany
    {
        return $this->belongsToMany(Grup::class, 'grup_alumne', 'alumne_id', 'grup_id');
    }

    public function tutors(): HasMany
    {
        return $this->hasMany(Tutor::class, 'alumne_id');
    }

    /** Apellidos juntos */
    public function getCognomsAttribute(): string
    {
        return trim(($this->cognom1 ?? '') . ' ' . ($this->cognom2 ?? ''));
    }

    /**
     * Nombre formateado según el formato del espacio.
     * 'cognoms_nom' => "García Pérez, Alan"
     * 'nom_cognoms' => "Alan García Pérez"
     */
    public function nomFormatat(?string $format = null): string
    {
        $format = $format ?? optional($this->espai)->format_nom ?? 'nom_cognoms';
        $cognoms = $this->cognoms;

        if ($format === 'cognoms_nom') {
            return $cognoms !== '' ? "{$cognoms}, {$this->nom}" : $this->nom;
        }

        return trim("{$this->nom} {$cognoms}");
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Genera un slug únic dins de l'espai a partir del nom complet.
     */
    public static function generarSlug(int $espaiId, string $nom, string $cognom1, ?string $cognom2 = null, ?int $ignorarId = null): string
    {
        $base = \Illuminate\Support\Str::slug(trim("{$nom} {$cognom1} {$cognom2}"));
        if ($base === '') {
            $base = 'alumne';
        }

        $slug = $base;
        $i = 2;

        while (
            static::where('espai_id', $espaiId)
                ->where('slug', $slug)
                ->when($ignorarId, fn($q) => $q->where('id', '!=', $ignorarId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        static::creating(function (Alumne $alumne) {
            if (empty($alumne->slug)) {
                $alumne->slug = static::generarSlug(
                    $alumne->espai_id,
                    $alumne->nom ?? '',
                    $alumne->cognom1 ?? '',
                    $alumne->cognom2 ?? null
                );
            }
        });
    }
}