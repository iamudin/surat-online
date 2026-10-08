<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Frontend;

use Illuminate\Http\Request;
use Closure;

class RedirectMiddleware
{
    public static function handle()
    {
        return function (Request $request, Closure $next) {
            $customDomain = get_option('surat-online-domain');
            $host = $request->getHost();
            $path = $request->path();

            // Cek apakah ini rute versi "pendek" (tanpa surat-online/)
            $isShortPath = !preg_match('/^surat-online(\/|$)/', ltrim($path, '/'));

            if ($customDomain) {
                if ($host !== $customDomain) {
                    // Jika akses dari domain utama tapi punya custom domain -> redirect ke custom domain
                    $newPath = preg_replace('/^surat-online\/?/', '', ltrim($path, '/'));
                    $url = $request->getScheme() . '://' . $customDomain . '/' . ltrim($newPath, '/');
                    if ($request->getQueryString()) {
                        $url .= '?' . $request->getQueryString();
                    }
                    return redirect()->to($url);
                } else {
                    // Jika di custom domain tapi pakai rute panjang -> redirect ke rute pendek
                    if (!$isShortPath) {
                        $newPath = preg_replace('/^surat-online\/?/', '', ltrim($path, '/'));
                        $url = $request->getScheme() . '://' . $customDomain . '/' . ltrim($newPath, '/');
                        if ($request->getQueryString()) {
                            $url .= '?' . $request->getQueryString();
                        }
                        return redirect()->to($url);
                    }
                }
            } else {
                // Jika TIDAK punya custom domain, maka WAJIB pakai rute panjang (surat-online/...)
                if ($isShortPath) {
                    abort(404);
                }

                // Jika diakses dari host asing (misal bekas custom domain) saat tidak ada setting custom domain,
                // arahkan kembali ke domain utama/tenant aktif (atau parked domain jika aktif).
                $isAllowedTenantHost = false;
                $activeDomain = parse_url(config('app.url'), PHP_URL_HOST);

                if (config('modules.multisite_enabled') && function_exists('tenant') && tenant()) {
                    $parkedDomain = (function_exists('get_option') ? get_option('parked_domain') : null);
                    $activeDomain = $parkedDomain ?: tenant()->domain;
                    if ($host === tenant()->domain || ($parkedDomain && $host === $parkedDomain)) {
                        $isAllowedTenantHost = true;
                    }
                }

                if (!$isAllowedTenantHost && $host !== $activeDomain && $activeDomain) {
                    $url = $request->getScheme() . '://' . $activeDomain . '/' . ltrim($path, '/');
                    if ($request->getQueryString()) {
                        $url .= '?' . $request->getQueryString();
                    }
                    return redirect()->to($url);
                }
            }

            return $next($request);
        };
    }
}
