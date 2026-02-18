<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const TYPE_USER = 'user';
    public const TYPE_USER_ASSINANTE = 'user_assinante';
    public const TYPE_ADM = 'adm';
    public const TYPE_COLABORADOR = 'colaborador';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }

        if (Str::startsWith($this->avatar_path, 'uploads/avatars/')) {
            return asset($this->avatar_path);
        }

        try {
            return Storage::disk('local')->temporaryUrl($this->avatar_path, now()->addMinutes(15));
        } catch (\Throwable) {
            return null;
        }
    }

    public function questaoRespostas(): HasMany
    {
        return $this->hasMany(QuestaoResposta::class);
    }

    public function feedbackTickets(): HasMany
    {
        return $this->hasMany(FeedbackTicket::class);
    }

    public function simuladoTentativas(): HasMany
    {
        return $this->hasMany(SimuladoTentativa::class);
    }
}
