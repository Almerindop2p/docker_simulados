<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackTicket extends Model
{
    public const STATUS_ABERTO = 'aberto';
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PROCESSANDO = 'processando';
    public const STATUS_CONCLUIDO = 'concluido';

    public const ALLOWED_STATUSES = [
        self::STATUS_ABERTO,
        self::STATUS_PENDENTE,
        self::STATUS_PROCESSANDO,
        self::STATUS_CONCLUIDO,
    ];

    protected $fillable = [
        'user_id',
        'nome',
        'email',
        'mensagem',
        'origem_rota',
        'pagina_url',
        'status',
        'observacao_admin',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
