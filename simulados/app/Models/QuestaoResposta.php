<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestaoResposta extends Model
{
    protected $table = 'questao_respostas';

    protected $fillable = [
        'user_id',
        'questao_id',
        'banca_id',
        'materia_id',
        'resposta_marcada',
        'gabarito',
        'acertou',
        'respondida_em',
    ];

    protected function casts(): array
    {
        return [
            'acertou' => 'boolean',
            'respondida_em' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questao(): BelongsTo
    {
        return $this->belongsTo(Questao::class);
    }

    public function banca(): BelongsTo
    {
        return $this->belongsTo(Banca::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }
}