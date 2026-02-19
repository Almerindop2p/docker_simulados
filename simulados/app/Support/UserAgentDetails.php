<?php

namespace App\Support;

class UserAgentDetails
{
    /**
     * @return array{browser:?string,browser_version:?string,operating_system:?string,device_type:string,device_model:?string}
     */
    public static function parse(?string $userAgent): array
    {
        $ua = (string) $userAgent;
        $uaLower = mb_strtolower($ua);

        [$browser, $browserVersion] = self::parseBrowser($ua);
        $os = self::parseOperatingSystem($uaLower);
        $deviceType = self::parseDeviceType($uaLower);
        $deviceModel = self::parseDeviceModel($ua);

        return [
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'operating_system' => $os,
            'device_type' => $deviceType,
            'device_model' => $deviceModel,
        ];
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private static function parseBrowser(string $ua): array
    {
        $rules = [
            'Edge' => '/Edg\/([0-9\.]+)/',
            'Opera' => '/OPR\/([0-9\.]+)/',
            'Chrome' => '/Chrome\/([0-9\.]+)/',
            'Firefox' => '/Firefox\/([0-9\.]+)/',
            'Safari' => '/Version\/([0-9\.]+).*Safari\//',
        ];

        foreach ($rules as $browser => $regex) {
            if (preg_match($regex, $ua, $matches) === 1) {
                return [$browser, $matches[1] ?? null];
            }
        }

        return [null, null];
    }

    private static function parseOperatingSystem(string $uaLower): ?string
    {
        if (str_contains($uaLower, 'windows')) {
            return 'Windows';
        }

        if (str_contains($uaLower, 'android')) {
            return 'Android';
        }

        if (str_contains($uaLower, 'iphone') || str_contains($uaLower, 'ipad') || str_contains($uaLower, 'ios')) {
            return 'iOS';
        }

        if (str_contains($uaLower, 'mac os') || str_contains($uaLower, 'macintosh')) {
            return 'macOS';
        }

        if (str_contains($uaLower, 'linux')) {
            return 'Linux';
        }

        return null;
    }

    private static function parseDeviceType(string $uaLower): string
    {
        $isTablet = preg_match('/ipad|tablet|kindle|silk|playbook|sm\-t/i', $uaLower) === 1;
        if ($isTablet) {
            return 'tablet';
        }

        $isMobile = preg_match('/mobi|iphone|ipod|android.*mobile|windows phone/i', $uaLower) === 1;
        if ($isMobile) {
            return 'mobile';
        }

        return 'desktop';
    }

    private static function parseDeviceModel(string $ua): ?string
    {
        $regexes = [
            '/\b(SM\-[A-Z0-9\-]{2,})\b/i',
            '/\b(MI\s?[A-Z0-9\-]{2,})\b/i',
            '/\b(Redmi\s?[A-Z0-9\-]{2,})\b/i',
            '/\b(Pixel\s?[A-Z0-9\s]{1,12})\b/i',
            '/\b(iPhone|iPad)\b/i',
        ];

        foreach ($regexes as $regex) {
            if (preg_match($regex, $ua, $matches) === 1) {
                $value = trim((string) ($matches[1] ?? ''));
                if ($value !== '') {
                    return mb_substr($value, 0, 120);
                }
            }
        }

        return null;
    }
}
