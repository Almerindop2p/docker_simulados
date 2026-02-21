<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaKeyword extends Model
{
    protected $table = 'meta_keywords';

    protected $fillable = [
        'keyword',
    ];
}

