<?php

namespace OpenAdminCore\Admin\MultiLanguage\Middlewares;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use OpenAdminCore\Admin\Facades\Admin;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use OpenAdminCore\Admin\MultiLanguage\Traits\LogsWithContext;
use OpenAdminCore\Admin\MultiLanguage\Octane\OctaneHandler;

class MultiLanguageMiddleware
{
    use LogsWithContext;

    /**
     * @var array Кэш схемы таблицы для оптимизации
     */
    private static array $schemaCache = [];

    public function __construct()
    {
        $this->initLogContext();
    }

    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        if (!MultiLanguage::config('enable', true)) {
            return $next($request);
        }

        try {
            $config = $this->getCachedConfig();
            $locale = $this->resolveLocale($request, $config);

            App::setLocale($locale);

            $this->handleAuthenticatedUser($locale);

            $this->log('debug', 'Locale resolved', [
                'locale' => $locale,
                'source' => $this->getLocaleSource($request, $config)
            ]);

        } catch (\Throwable $e) {
            $this->log('error', 'Failed to resolve locale', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (!app()->environment('production')) {
                throw $e;
            }
        }

        $this->logPerformance('locale_resolution', microtime(true) - $startTime);

        return $next($request);
    }

    /**
     * Получить кэшированную конфигурацию
     */
    private function getCachedConfig(): array
    {
        // Octane memory cache
        if (OctaneHandler::isActive() && ($config = OctaneHandler::get('config'))) {
            return $config;
        }

        $startTime = microtime(true);

        try {
            $config = app()->environment('production')
                ? Cache::remember('multi-language.config', 3600, fn() => MultiLanguage::config())
                : MultiLanguage::config();

            $this->logPerformance('config_cache', microtime(true) - $startTime);

            return $config;

        } catch (\Throwable $e) {
            $this->log('error', 'Failed to get config', ['error' => $e->getMessage()]);

            return [
                'languages' => ['en' => 'English'],
                'default' => 'en',
                'cookie-name' => 'locale'
            ];
        }
    }

    /**
     * Определить локаль из различных источников
     */
    private function resolveLocale(Request $request, array $config): string
    {
        $languages = $config['languages'] ?? [];
        $cookieName = $config['cookie-name'] ?? 'locale';
        $default = $config['default'] ?? 'en';

        $localeFromCookie = Cookie::get($cookieName);

        if ($localeFromCookie && array_key_exists($localeFromCookie, $languages)) {
            return $localeFromCookie;
        }

        if (empty($localeFromCookie) && $browserLocale = $this->getBrowserLocale($request, $languages)) {
            $this->setLocaleCookie($browserLocale, $cookieName, $config);
            return $browserLocale;
        }

        if ($localeFromCookie && !array_key_exists($localeFromCookie, $languages)) {
            $this->logSecurity('Invalid locale cookie attempt', [
                'locale' => $localeFromCookie,
                'valid_locales' => array_keys($languages)
            ]);
        }

        $this->setLocaleCookie($default, $cookieName, $config);
        return $default;
    }

    /**
     * Получить локаль из браузера
     */
    private function getBrowserLocale(Request $request, array $languages): ?string
    {
        foreach ($request->getLanguages() as $browserLocale) {
            $localeCode = substr($browserLocale, 0, 2);
            if (array_key_exists($localeCode, $languages)) {
                return $localeCode;
            }
        }
        return null;
    }

    /**
     * Установить cookie с локалью
     */
    private function setLocaleCookie(string $locale, string $cookieName, array $config): void
    {
        Cookie::queue(
            $cookieName,
            $locale,
            60 * 24 * 30,
            '/',
            null,
            config('session.secure', request()->secure()),
            true,
            false,
            config('session.same_site') ?? 'lax'
        );
    }

    /**
     * Обработка авторизованного пользователя
     */
    private function handleAuthenticatedUser(string $locale): void
    {
        $guard = $this->guard();

        if (!$guard->check()) {
            return;
        }

        $user = $guard->user();

        if ($user->locale === $locale) {
            return;
        }

        $startTime = microtime(true);
        $this->updateUserLocale($user, $locale);
        $this->logPerformance('user_locale_update', microtime(true) - $startTime);
    }

    /**
     * Проверить существование колонки locale с кэшированием
     */
    private function hasLocaleColumn($user): bool
    {
        $table = $user->getTable();

        if (!isset(self::$schemaCache[$table])) {
            $startTime = microtime(true);

            self::$schemaCache[$table] = Schema::hasColumn($table, 'locale');

            // Для Octane сохраняем в memory cache
            if (OctaneHandler::isActive()) {
                OctaneHandler::set("schema.{$table}", self::$schemaCache[$table]);
            }

            $this->logPerformance('schema_check', microtime(true) - $startTime);
        }

        return self::$schemaCache[$table];
    }

    /**
     * Обновление локали пользователя в БД
     */
    private function updateUserLocale($user, string $locale): void
    {
        if (!$this->hasLocaleColumn($user)) {
            $this->log('debug', 'Locale column not found', ['table' => $user->getTable()]);
            return;
        }

        try {
            $oldLocale = $user->locale;
            $user->locale = $locale;
            $user->saveQuietly(['locale']);

            $this->log('info', 'User locale updated', [
                'user_id' => $user->id,
                'old_locale' => $oldLocale,
                'new_locale' => $locale
            ]);

        } catch (\Exception $e) {
            $this->log('error', 'Failed to save user locale', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Определить источник локали
     */
    private function getLocaleSource(Request $request, array $config): string
    {
        $cookieName = $config['cookie-name'] ?? 'locale';

        return match (true) {
            Cookie::has($cookieName) => 'cookie',
            $this->getBrowserLocale($request, $config['languages'] ?? []) !== null => 'browser',
            default => 'default'
        };
    }

    /**
     * Сбросить кэш схемы (для Octane)
     */
    public static function resetSchemaCache(): void
    {
        self::$schemaCache = [];
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard|StatefulGuard
     */
    protected function guard(): Guard|StatefulGuard
    {
        return Admin::guard();
    }
}
