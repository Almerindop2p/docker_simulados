<?php

namespace App\Models;

use App\Models\Concerns\HasCountryFlagIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMetricConsent extends Model
{
    use HasCountryFlagIcon;

    protected $fillable = [
        'user_id',
        'is_granted',
        'granted_at',
        'ip_address',
        'user_agent',
        'country',
        'country_code',
        'state',
        'city',
        'neighborhood',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
        'granted_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
