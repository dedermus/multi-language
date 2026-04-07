<?php

use OpenAdminCore\Admin\MultiLanguage\Enums\Locale;

return [
    /*
    |--------------------------------------------------------------------------
    | Основные настройки мультиязычности
    |--------------------------------------------------------------------------
    */

    'enable' => env('MULTI_LANGUAGE_ENABLE', true),

    'languages' => Locale::toConfigArray(),

    'default' => env('MULTI_LANGUAGE_DEFAULT', 'ru'),

    'cookie-name' => env('MULTI_LANGUAGE_COOKIE', 'locale'),

    'show-login-page' => env('MULTI_LANGUAGE_SHOW_LOGIN', true),

    'show-navbar' => env('MULTI_LANGUAGE_SHOW_NAVBAR', true),

    /*
    |--------------------------------------------------------------------------
    | Настройки логирования
    |--------------------------------------------------------------------------
    */

    'log_channel' => env('MULTI_LANGUAGE_LOG_CHANNEL', 'stack'),

    'log_security' => env('MULTI_LANGUAGE_LOG_SECURITY', true),

    'log_performance' => env('MULTI_LANGUAGE_LOG_PERFORMANCE', false),

    /*
    |--------------------------------------------------------------------------
    | Настройки кэширования
    |--------------------------------------------------------------------------
    */

    'cache_ttl' => env('MULTI_LANGUAGE_CACHE_TTL', 3600),

    'cache_key' => 'multi-language.config',

    /*
    |--------------------------------------------------------------------------
    | Настройки производительности
    |--------------------------------------------------------------------------
    */

    'slow_threshold_ms' => env('MULTI_LANGUAGE_SLOW_THRESHOLD', 100),

    'prefetch_browser_locale' => env('MULTI_LANGUAGE_PREFETCH_BROWSER', true),

    'save_to_database' => env('MULTI_LANGUAGE_SAVE_TO_DB', true),

   /*
   |--------------------------------------------------------------------------
   | API настройки
   |--------------------------------------------------------------------------
   */
    'api' => [
        'enabled' => env('MULTI_LANGUAGE_API_ENABLED', true),
        'prefix' => env('MULTI_LANGUAGE_API_PREFIX', 'admin/api'),
        'rate_limiting' => env('MULTI_LANGUAGE_API_RATE_LIMIT', 60),
        'cache_translations' => env('MULTI_LANGUAGE_CACHE_TRANSLATIONS', true),
        'cache_ttl' => env('MULTI_LANGUAGE_TRANSLATIONS_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки переводов
    |--------------------------------------------------------------------------
    */
    'translations' => [
        'load_vendor' => env('MULTI_LANGUAGE_LOAD_VENDOR_TRANSLATIONS', true),
        'fallback' => env('MULTI_LANGUAGE_FALLBACK_LOCALE', 'en'),
    ],
];
