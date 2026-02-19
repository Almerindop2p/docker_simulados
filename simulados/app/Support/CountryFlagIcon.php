<?php

namespace App\Support;

class CountryFlagIcon
{
    public const DEFAULT_ICON = 'assets/flags/_default.svg';

    public static function url(?string $countryCode): string
    {
        $code = self::normalizeCode($countryCode);

        if ($code !== null) {
            $relativePath = "assets/flags/{$code}.svg";
            if (is_file(public_path($relativePath))) {
                return asset($relativePath);
            }
        }

        return asset(self::DEFAULT_ICON);
    }

    public static function normalizeCode(?string $countryCode): ?string
    {
        $code = strtolower(preg_replace('/[^a-z]/i', '', (string) $countryCode));
        if (strlen($code) !== 2) {
            return null;
        }

        return $code;
    }
}
