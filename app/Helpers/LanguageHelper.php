<?php

namespace App\Helpers;

class LanguageHelper
{
    public static function getCurrentLang(): string
    {
        // For API requests, use app locale (set by Accept-Language header)
        if (request()->is('api/*')) {
            return app()->getLocale();
        }

        // For admin panel, use session
        return session('admin_lang', 'ru');
    }

    public static function setCurrentLang(string $langCode): void
    {
        session(['admin_lang' => $langCode]);
    }
}
