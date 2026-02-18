<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfiguration extends Model
{
    public const SINGLETON_ID = 1;

    protected $fillable = [
        'adsense_enabled',
        'adsense_head_script',
    ];

    protected $casts = [
        'adsense_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => self::SINGLETON_ID],
            [
                'adsense_enabled' => false,
                'adsense_head_script' => null,
            ]
        );
    }
}
