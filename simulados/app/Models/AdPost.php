<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPost extends Model
{
    public const FORMAT_HORIZONTAL = 'horizontal';
    public const FORMAT_VERTICAL = 'vertical';

    public const GLOBAL_HORIZONTAL_SLUG = 'global-horizontal';
    public const GLOBAL_VERTICAL_SLUG = 'global-vertical';

    protected $fillable = [
        'title',
        'slug',
        'format',
        'is_active',
        'embed_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
