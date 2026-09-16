<?php

namespace App\Support;

class LoginDevice
{
    /** Browser-reported hints, not a verified hardware identity. */
    public static function describe(?string $userAgent): array
    {
        $agent = strtolower($userAgent ?? '');
        $platform = match (true) {
            str_contains($agent, 'iphone'), str_contains($agent, 'ipad') => 'iOS',
            str_contains($agent, 'android') => 'Android',
            str_contains($agent, 'windows') => 'Windows',
            str_contains($agent, 'cros') => 'ChromeOS',
            str_contains($agent, 'macintosh'), str_contains($agent, 'mac os') => 'macOS',
            str_contains($agent, 'linux') => 'Linux',
            default => 'OS not recorded',
        };
        $browser = match (true) {
            str_contains($agent, 'edg/') , str_contains($agent, 'edga/'), str_contains($agent, 'edgios/') => 'Microsoft Edge',
            str_contains($agent, 'opr/'), str_contains($agent, 'opera') => 'Opera',
            str_contains($agent, 'samsungbrowser/') => 'Samsung Internet',
            str_contains($agent, 'firefox/'), str_contains($agent, 'fxios/') => 'Firefox',
            str_contains($agent, 'chrome/'), str_contains($agent, 'crios/') => 'Chrome',
            str_contains($agent, 'safari/') => 'Safari',
            default => 'Browser not recorded',
        };
        $device = match (true) {
            str_contains($agent, 'ipad'), str_contains($agent, 'tablet'),
                str_contains($agent, 'android') && !str_contains($agent, 'mobile') => 'Tablet',
            str_contains($agent, 'mobile'), str_contains($agent, 'iphone') => 'Mobile phone',
            in_array($platform, ['Windows', 'macOS', 'Linux', 'ChromeOS'], true) => 'Computer',
            default => 'Device not recorded',
        };

        return [
            'device' => $device,
            'device_icon' => match ($device) {
                'Tablet' => 'tablet-alt',
                'Mobile phone' => 'mobile-alt',
                'Computer' => 'laptop',
                default => 'question-circle',
            },
            'browser' => $browser,
            'platform' => $platform,
        ];
    }
}
