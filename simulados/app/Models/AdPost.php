<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPost extends Model
{
    public const FORMAT_HORIZONTAL = 'horizontal';
    public const FORMAT_VERTICAL = 'vertical';
    public const FORMATS = [
        self::FORMAT_HORIZONTAL,
        self::FORMAT_VERTICAL,
    ];

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

    public static function formatOptions(): array
    {
        return [
            self::FORMAT_HORIZONTAL => 'Horizontal',
            self::FORMAT_VERTICAL => 'Vertical',
        ];
    }

    public static function formatLabel(?string $format): string
    {
        return static::formatOptions()[$format ?? ''] ?? 'Nao definido';
    }
}
