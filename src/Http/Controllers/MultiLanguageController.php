<?php

namespace OpenAdminCore\Admin\MultiLanguage\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Application;
use OpenAdminCore\Admin\MultiLanguage\Traits\LogsWithContext;
use Illuminate\Validation\ValidationException;

class MultiLanguageController extends Controller
{
    use LogsWithContext;

    public function __construct()
    {
        $this->initLogContext();
    }

    /**
     * Установка локали (PHP 8.3 attributes)
     */
    public function locale(Request $request): JsonResponse|\Illuminate\Http\Response
    {
        \Log::info('Locale change requested', [
            'locale' => $request->input('locale'),
            'cookie' => $request->cookie(MultiLanguage::config('cookie-name', 'locale')),
            'headers' => $request->headers->all(),
            'session' => session()->all()
        ]);
        $startTime = microtime(true);

        try {
            $validated = $request->validate([
                'locale' => ['required', 'string', 'size:2', 'alpha'],
                'username' => ['sometimes', 'string', 'max:255'],
                'password' => ['sometimes', 'string'],
                'remember' => ['sometimes', 'boolean'],
            ]);

            // Сохранение данных формы (только для логина)
            if ($request->hasAny(['username', 'password', 'remember'])) {
                session()->flash('form_data', [
                    'username' => $validated['username'] ?? null,
                    'password' => $validated['password'] ?? null,
                    'remember' => $validated['remember'] ?? false,
                ]);
            }

            $locale = $validated['locale'];
            $languages = MultiLanguage::config('languages', []);
            $cookieName = MultiLanguage::config('cookie-name', 'locale');

            if (!array_key_exists($locale, $languages)) {
                $this->logSecurity('Invalid locale attempt', [
                    'locale' => $locale,
                    'valid_locales' => array_keys($languages)
                ]);

                return response()->json(['error' => 'Invalid locale'], 400);
            }

            // Устанавливаем локаль
            App::setLocale($locale);
            Config::set('app.locale', $locale);

            // Создаем cookie
            $cookie = cookie(
                $cookieName,
                $locale,
                60 * 24 * 30,
                '/',
                null,
                config('app.env') === 'production',
                true,
                false,
                config('session.same_site') ?? 'lax'
            );

            $duration = microtime(true) - $startTime;
            $this->logPerformance('locale_change', $duration);

            $this->log('info', 'Locale changed successfully', [
                'locale' => $locale,
                'user_id' => auth()->id() ?? 'guest'
            ]);

            return response('ok')
                ->withCookie($cookie)
                ->header('X-Locale-Changed', $locale)
                ->header('X-Duration', round($duration * 1000, 2) . 'ms');

        } catch (ValidationException $e) {
            $this->log('warning', 'Locale validation failed', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            $this->log('error', 'Locale change error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Отображение страницы логина
     */
    public function getLogin(): Factory|View|Application
    {
        $startTime = microtime(true);

        $languages = MultiLanguage::config('languages', []);
        $cookieName = MultiLanguage::config('cookie-name', 'locale');

        $current = MultiLanguage::config('default', 'en');

        if (Cookie::has($cookieName)) {
            $cookieLocale = Cookie::get($cookieName);

            if (array_key_exists($cookieLocale, $languages)) {
                $current = $cookieLocale;
                App::setLocale($current);
                Config::set('app.locale', $current);
            }
        }

        $duration = microtime(true) - $startTime;
        $this->logPerformance('login_page_render', $duration);

        return view('multi-language::login', [
            'languages' => $languages,
            'current' => $current
        ]);
    }

    /**
     * API метод для получения доступных языков
     */
    public function getLanguages(): JsonResponse
    {
        $languages = MultiLanguage::config('languages', []);
        $current = App::getLocale();

        $this->log('debug', 'Languages requested', [
            'languages_count' => count($languages),
            'current' => $current
        ]);

        return response()->json([
            'languages' => $languages,
            'current' => $current,
            'default' => MultiLanguage::config('default', 'en')
        ]);
    }
}
