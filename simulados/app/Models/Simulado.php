<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Simulado extends Model
{
    public const VISIBILIDADE_PUBLICO = 'publico';
    public const VISIBILIDADE_PRIVADO = 'privado';
    public const VISIBILIDADE_ASSINANTES = 'assinantes';
    public const VISIBILIDADE_NAO_LISTADO = 'nao_listado';

    public const VISIBILIDADES = [
        self::VISIBILIDADE_PUBLICO,
        self::VISIBILIDADE_PRIVADO,
        self::VISIBILIDADE_ASSINANTES,
        self::VISIBILIDADE_NAO_LISTADO,
    ];

    protected $table = 'simulados';

    protected $fillable = [
        'name',
        'slug',
        'visibilidade',
        'descricao',
        'imagem_destaque_path',
    ];

    public function questoes(): HasMany
    {
        return $this->hasMany(Questao::class);
    }

    public function tentativas(): HasMany
    {
        return $this->hasMany(SimuladoTentativa::class);
    }

    public static function visibilidadeLabel(?string $visibilidade): string
    {
        return match ($visibilidade) {
            self::VISIBILIDADE_PUBLICO => 'Publico',
            self::VISIBILIDADE_PRIVADO => 'Privado',
            self::VISIBILIDADE_ASSINANTES => 'Usuarios assinantes',
            self::VISIBILIDADE_NAO_LISTADO => 'Nao listado',
            default => 'Nao informado',
        };
    }

    public function getImagemDestaqueUrlAttribute(): ?string
    {
        if (!$this->imagem_destaque_path) {
            return null;
        }

        return Storage::disk('public')->url($this->imagem_destaque_path);
    }
}
