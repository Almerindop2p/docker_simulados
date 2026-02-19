<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVisitCounter extends Model
{
    protected $fillable = [
        'user_id',
        'anonymous_id',
        'visitor_key',
        'route_name',
        'page_path',
        'page_hash',
        'country',
        'state',
        'city',
        'location_hash',
        'visits_count',
        'first_visited_at',
        'last_visited_at',
    ];

    protected $casts = [
        'first_visited_at' => 'datetime',
        'last_visited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
