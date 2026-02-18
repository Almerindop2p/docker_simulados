<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimuladoTentativaResposta extends Model
{
    protected $table = 'simulado_tentativa_respostas';

    protected $fillable = [
        'tentativa_id',
        'questao_id',
        'question_index',
        'resposta_marcada',
        'gabarito',
        'acertou',
        'elapsed_seconds',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'acertou' => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    public function tentativa(): BelongsTo
    {
        return $this->belongsTo(SimuladoTentativa::class, 'tentativa_id');
    }

    public function questao(): BelongsTo
    {
        return $this->belongsTo(Questao::class);
    }
}

