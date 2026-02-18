<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimuladoTentativa extends Model
{
    public const STATUS_ABERTO = 'aberto';
    public const STATUS_CONCLUIDO = 'concluido';

    protected $table = 'simulado_tentativas';

    protected $fillable = [
        'user_id',
        'simulado_id',
        'status',
        'questoes_snapshot',
        'total_questoes',
        'questoes_respondidas',
        'acertos',
        'erros',
        'current_index',
        'started_at',
        'finished_at',
        'total_elapsed_seconds',
    ];

    protected function casts(): array
    {
        return [
            'questoes_snapshot' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function simulado(): BelongsTo
    {
        return $this->belongsTo(Simulado::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(SimuladoTentativaResposta::class, 'tentativa_id');
    }
}

