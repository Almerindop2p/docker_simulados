<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Banca extends Model
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
