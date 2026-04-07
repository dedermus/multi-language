<?php

namespace OpenAdminCore\Admin\MultiLanguage\Middlewares;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use Illuminate\Support\Facades\Log;

class MultiLanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        // Установка исключений для маршрутов
        config(['admin.auth.excepts' => ['auth/login','locale']]);

        // Получение доступных языков и имени куки из конфигурации
        $languages = MultiLanguage::config('languages');
        $cookie_name = MultiLanguage::config('cookie-name', 'locale');

        // Проверка наличия куки и соответствия доступным языкам
        if (Cookie::has($cookie_name) && array_key_exists(Cookie::get($cookie_name), $languages)) {
            App::setLocale(Cookie::get($cookie_name));
        } else {
            // Установка локали по умолчанию
            $default = MultiLanguage::config('default', 'en');
            App::setLocale($default);

            // Логирование, если куки не соответствуют доступным языкам
            if (!Cookie::has($cookie_name)) {
                Log::warning("Invalid locale cookie value: " . Cookie::get($cookie_name));
            }
        }
        return $next($request);
    }
}
