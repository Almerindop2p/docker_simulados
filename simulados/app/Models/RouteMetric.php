<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteMetric extends Model
{
    protected $fillable = [
        'user_id',
        'consent_mode',
        'route_name',
        'page_url',
        'path',
        'referrer',
        'ip_address',
        'user_agent',
        'browser',
        'browser_version',
        'device_type',
        'operating_system',
        'country',
        'state',
        'city',
        'neighborhood',
        'latitude',
        'longitude',
        'timezone',
        'language',
        'viewport_width',
        'viewport_height',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
