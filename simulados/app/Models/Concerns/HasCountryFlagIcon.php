<?php

namespace App\Models\Concerns;

use App\Support\CountryFlagIcon;

trait HasCountryFlagIcon
{
    public function getCountryFlagCodeAttribute(): ?string
    {
        return CountryFlagIcon::normalizeCode((string) ($this->country_code ?? ''));
    }

    public function getCountryFlagUrlAttribute(): string
    {
        return CountryFlagIcon::url((string) ($this->country_code ?? ''));
    }
}
