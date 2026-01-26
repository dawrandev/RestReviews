<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from Accept-Language header or default to 'kk'
        $locale = $request->header('Accept-Language', 'kk');

        // Extract the language code (e.g., 'kk-KK' becomes 'kk')
        $locale = strtolower(substr($locale, 0, 2));

        // Set available locales
        $availableLocales = ['uz', 'ru', 'kk', 'en'];

        // Fallback to 'kk' if locale is not supported
        if (!in_array($locale, $availableLocales)) {
            $locale = 'kk';
        }

        // Set application locale
        app()->setLocale($locale);

        return $next($request);
    }
}
