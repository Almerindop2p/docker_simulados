<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function questoes(): HasMany
    {
        return $this->hasMany(Questao::class);
    }
}
