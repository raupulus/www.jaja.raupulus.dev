<?php

namespace App\Helpers;

use Illuminate\Http\Request;

/**
 * Helper centralizado para resolver la IP real del cliente respetando proxies de confianza.
 */
class IpHelper
{
    /**
     * Devuelve la IP real del cliente validando cabeceras de proxy únicamente
     * cuando la petición entra a través de un proxy confiable configurado en TrustProxies.
     */
    public static function getClientIp(Request $request): string
    {
        if ($request->isFromTrustedProxy()) {
            $cloudflareIp = $request->header('CF-Connecting-IP');

            if (is_string($cloudflareIp)) {
                $candidate = trim(explode(',', $cloudflareIp)[0]);

                if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $candidate;
                }
            }
        }

        return $request->ip() ?? '127.0.0.1';
    }
}
