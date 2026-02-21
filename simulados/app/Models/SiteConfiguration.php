<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfiguration extends Model
{
    public const SINGLETON_ID = 1;

    protected $fillable = [
        'adsense_enabled',
        'feedback_feed_enabled',
        'adsense_head_script',
        'custom_html_code',
    ];

    protected $casts = [
        'adsense_enabled' => 'boolean',
        'feedback_feed_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => self::SINGLETON_ID],
            [
                'adsense_enabled' => false,
                'feedback_feed_enabled' => true,
                'adsense_head_script' => null,
                'custom_html_code' => null,
            ]
        );
    }
}
