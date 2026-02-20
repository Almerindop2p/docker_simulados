<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFeedbackPromptState extends Model
{
    protected $fillable = [
        'user_id',
        'cooldown_until',
        'last_prompt_at',
        'last_sent_at',
        'last_dismissed_at',
    ];

    protected $casts = [
        'cooldown_until' => 'datetime',
        'last_prompt_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'last_dismissed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

