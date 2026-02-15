<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Questao extends Model
{
    protected $table = 'questoes';

    protected $fillable = [
        'banca_id',
        'materia_id',
        'enunciado',
        'alternativa_a',
        'alternativa_b',
        'alternativa_c',
        'alternativa_d',
        'alternativa_e',
        'gabarito',
        'explicacao',
        'keywords',
    ];

    public function banca(): BelongsTo
    {
        return $this->belongsTo(Banca::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function cargos(): BelongsToMany
    {
        return $this->belongsToMany(Cargo::class, 'cargo_questao');
    }

    public function scopeFiltrarPorBanca(Builder $query, int $bancaId): Builder
    {
        return $query->where('banca_id', $bancaId);
    }

    public function scopeFiltrarPorMateria(Builder $query, int $materiaId): Builder
    {
        return $query->where('materia_id', $materiaId);
    }

    public function scopeFiltrarPorCargo(Builder $query, int $cargoId): Builder
    {
        return $query->whereHas('cargos', function (Builder $subQuery) use ($cargoId) {
            $subQuery->where('cargos.id', $cargoId);
        });
    }
}