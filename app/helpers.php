<?php

use App\Models\Setting;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('ipCheck')) {
    /**
     * IP/CIDR Check allow.
     *
     * @param string $ip
     * @param array $whitelist
     * @return bool
     */
    function ipCheck(string $ip, array $whitelist): bool
    {
        foreach ($whitelist as $range) {
            if (!str_contains($range, '/'))
                $range .= '/32';

            // $range is in IP/CIDR format e.g 127.0.0.1/24
            [$range, $netmask] = explode('/', $range, 2);
            $rangeDecimal    = ip2long($range);
            $ipDecimal       = ip2long($ip);
            $wildcardDecimal = pow(2, (32 - $netmask)) - 1;
            $netmask_decimal = ~$wildcardDecimal;

            // Check IP
            if (($ipDecimal & $netmask_decimal) == ($rangeDecimal & $netmask_decimal))
                return true;
        }

        return false;
    }
}
