<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cargo extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function questoes(): BelongsToMany
    {
        return $this->belongsToMany(Questao::class, 'cargo_questao');
    }
}
