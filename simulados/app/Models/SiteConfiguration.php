<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfiguration extends Model
{
    public const SINGLETON_ID = 1;

    protected $fillable = [
        'adsense_enabled',
        'feedback_feed_enabled',
        'recaptcha_enabled',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'adsense_head_script',
        'custom_html_code',
    ];

    protected $casts = [
        'adsense_enabled' => 'boolean',
        'feedback_feed_enabled' => 'boolean',
        'recaptcha_enabled' => 'boolean',
        'recaptcha_site_key' => 'encrypted',
        'recaptcha_secret_key' => 'encrypted',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => self::SINGLETON_ID],
            [
                'adsense_enabled' => false,
                'feedback_feed_enabled' => true,
                'recaptcha_enabled' => false,
                'recaptcha_site_key' => null,
                'recaptcha_secret_key' => null,
                'adsense_head_script' => null,
                'custom_html_code' => null,
            ]
        );
    }
}
