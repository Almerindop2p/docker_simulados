<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackTicket extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'email',
        'mensagem',
        'origem_rota',
        'pagina_url',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

