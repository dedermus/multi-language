<?php

namespace OpenAdminCore\Admin\MultiLanguage\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use OpenAdminCore\Admin\Facades\Admin;
use OpenAdminCore\Admin\MultiLanguage\Enums\Locale;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use OpenAdminCore\Admin\MultiLanguage\Traits\ApiResponse;
use OpenAdminCore\Admin\MultiLanguage\Traits\LogsWithContext;
use Symfony\Component\HttpFoundation\Response;

class MultiLanguageApiController extends Controller
{
    use ApiResponse, LogsWithContext;

    public function __construct()
    {
        $this->initLogContext();
    }

    /**
     * Получить список доступных языков
     *
     * GET /admin/api/languages
     */
    public function getLanguages(): JsonResponse
    {
        $languages = MultiLanguage::config('languages', []);
        $current = App::getLocale();
        $default = MultiLanguage::config('default', 'ru');

        $this->log('debug', 'Languages requested via API', [
            'count' => count($languages),
            'current' => $current
        ]);

        return $this->languagesResponse(
            $this->formatLanguages($languages),
            $current,
            $default
        );
    }

    /**
     * Получить текущую локаль (из cookie/браузера)
     *
     * GET /admin/api/locale
     */
    public function getLocale(Request $request): JsonResponse
    {
        $cookieName = MultiLanguage::config('cookie-name', 'locale');
        $default = MultiLanguage::config('default', 'ru');

        // Из cookie
        $locale = $request->cookie($cookieName);
        $source = 'cookie';

        // Из браузера
        if (!$locale) {
            $locale = $this->detectBrowserLocale($request);
            $source = 'browser';
        }

        // По умолчанию
        if (!$locale) {
            $locale = $default;
            $source = 'default';
        }

        return $this->currentLocaleResponse(
            $locale,
            $source,
            $this->getLocaleName($locale)
        );
    }

    /**
     * Установить локаль (cookie)
     *
     * POST /admin/api/locale
     */
    public function setLocale(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'locale' => ['required', 'string', 'size:2', 'alpha'],
            'remember' => ['sometimes', 'boolean']
        ], [
            'required' => $this->translate('locale_required'),
            'size' => $this->translate('locale_size'),
            'alpha' => $this->translate('locale_alpha')
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(
                $validator->errors(),
                'locale_invalid'
            );
        }

        $locale = $request->input('locale');
        $languages = MultiLanguage::config('languages', []);
        $cookieName = MultiLanguage::config('cookie-name', 'locale');

        if (!array_key_exists($locale, $languages)) {
            return $this->errorResponse(
                'locale_not_supported',
                Response::HTTP_BAD_REQUEST,
                ['valid_locales' => array_keys($languages)],
                ['locale' => $locale]
            );
        }

        // Устанавливаем локаль в приложении
        App::setLocale($locale);

        // Создаем cookie
        $cookie = cookie(
            $cookieName,
            $locale,
            $request->input('remember', true) ? 60 * 24 * 30 : 60 * 24,
            '/',
            null,
            config('app.env') === 'production',
            true,
            false,
            config('session.same_site') ?? 'lax'
        );

        // Если пользователь авторизован, сохраняем в БД
        if (Admin::guard()->check()) {
            $this->saveUserLocale(Admin::user(), $locale);
        }

        $this->log('info', 'Locale changed via API', [
            'locale' => $locale,
            'user_id' => Admin::guard()->id() ?? 'guest',
            'remember' => $request->input('remember', true)
        ]);

        return $this->localeChangedResponse(
            $locale,
            $this->getLocaleName($locale)
        )->withCookie($cookie);
    }

    /**
     * Получить локаль текущего пользователя (из БД)
     *
     * GET /admin/api/locale/user
     */
    public function getUserLocale(): JsonResponse
    {
        if (!Admin::guard()->check()) {
            return $this->errorResponse('unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $user = Admin::user();
        $locale = $user->locale ?? MultiLanguage::config('default', 'ru');

        return $this->userLocaleResponse(
            $user,
            $locale,
            $this->getLocaleName($locale)
        );
    }

    /**
     * Обновить локаль пользователя в БД
     *
     * PUT /admin/api/locale/user
     */
    public function updateUserLocale(Request $request): JsonResponse
    {
        if (!Admin::guard()->check()) {
            return $this->errorResponse('unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'locale' => ['required', 'string', 'size:2', 'alpha']
        ], [
            'required' => $this->translate('locale_required'),
            'size' => $this->translate('locale_size'),
            'alpha' => $this->translate('locale_alpha')
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(
                $validator->errors(),
                'locale_invalid'
            );
        }

        $locale = $request->input('locale');
        $languages = MultiLanguage::config('languages', []);

        if (!array_key_exists($locale, $languages)) {
            return $this->errorResponse(
                'locale_not_supported',
                Response::HTTP_BAD_REQUEST,
                ['valid_locales' => array_keys($languages)],
                ['locale' => $locale]
            );
        }

        $user = Admin::user();
        $oldLocale = $user->locale;

        try {
            $user->locale = $locale;
            $user->saveQuietly(['locale']);

            $this->log('info', 'User locale updated via API', [
                'user_id' => $user->id,
                'old_locale' => $oldLocale,
                'new_locale' => $locale
            ]);

            return $this->userLocaleUpdatedResponse(
                $user,
                $locale,
                $oldLocale,
                $this->getLocaleName($locale)
            );

        } catch (\Exception $e) {
            $this->log('error', 'Failed to update user locale via API', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('server_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Получить переводы для локали
     *
     * GET /admin/api/translations/{locale}
     */
    public function getTranslations(string $locale): JsonResponse
    {
        $languages = MultiLanguage::config('languages', []);

        if (!array_key_exists($locale, $languages)) {
            return $this->errorResponse(
                'locale_not_supported',
                Response::HTTP_BAD_REQUEST,
                [],
                ['locale' => $locale]
            );
        }

        // Кэшируем переводы на 1 час
        $translations = Cache::remember("translations.{$locale}", 3600, function () use ($locale) {
            return $this->loadTranslations($locale);
        });

        return $this->translationsResponse($locale, $translations);
    }

    /**
     * Получить группу переводов
     *
     * GET /admin/api/translations/{locale}/{group}
     */
    public function getTranslationGroup(string $locale, string $group): JsonResponse
    {
        $languages = MultiLanguage::config('languages', []);

        if (!array_key_exists($locale, $languages)) {
            return $this->errorResponse(
                'locale_not_supported',
                Response::HTTP_BAD_REQUEST,
                [],
                ['locale' => $locale]
            );
        }

        $translations = Cache::remember("translations.{$locale}.{$group}", 3600, function () use ($locale, $group) {
            return $this->loadTranslationGroup($locale, $group);
        });

        return $this->translationGroupResponse($locale, $group, $translations);
    }

    /**
     * Валидация локали
     *
     * POST /admin/api/validate
     */
    public function validateLocale(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'locale' => ['required', 'string', 'size:2', 'alpha']
        ], [
            'required' => $this->translate('locale_required'),
            'size' => $this->translate('locale_size'),
            'alpha' => $this->translate('locale_alpha')
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(
                $validator->errors(),
                'locale_invalid'
            );
        }

        $locale = $request->input('locale');
        $languages = MultiLanguage::config('languages', []);
        $isValid = array_key_exists($locale, $languages);

        return $this->validationResponse(
            $locale,
            $isValid,
            array_keys($languages)
        );
    }

    /**
     * Статистика использования языков (только для админов)
     *
     * GET /admin/api/stats
     */
    public function getStats(): JsonResponse
    {
        if (!Admin::guard()->check()) {
            return $this->errorResponse('unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        // Проверяем права администратора
        if (!Admin::user()->isRole('administrator')) {
            return $this->errorResponse('forbidden', Response::HTTP_FORBIDDEN);
        }

        try {
            $userModel = Admin::user()->getModel();
            $totalUsers = $userModel::count();

            $stats = $userModel::select('locale', \DB::raw('count(*) as total'))
                ->groupBy('locale')
                ->orderBy('total', 'desc')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->locale => [
                        'count' => $item->total,
                        'percentage' => round(($item->total / $totalUsers) * 100, 2),
                        'name' => $this->getLocaleName($item->locale)
                    ]];
                });

            return $this->statsResponse(
                $stats->toArray(),
                MultiLanguage::config('languages', []),
                $totalUsers
            );

        } catch (\Exception $e) {
            $this->log('error', 'Failed to get locale stats', [
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('server_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * ---------- Приватные методы ----------
     */

    /**
     * Форматировать список языков
     */
    private function formatLanguages(array $languages): array
    {
        $result = [];

        foreach ($languages as $code => $name) {
            $locale = Locale::tryFrom($code);

            $result[] = [
                'code' => $code,
                'name' => $name,
                'native_name' => $locale?->label() ?? $name,
                'flag' => $locale?->flag(),
                'direction' => $locale?->direction() ?? 'ltr',
                'date_format' => $locale?->dateFormat() ?? 'Y-m-d',
                'timezone' => $locale?->timezone() ?? 'UTC',
                'is_rtl' => ($locale?->direction() ?? 'ltr') === 'rtl'
            ];
        }

        return $result;
    }

    /**
     * Определить локаль из браузера
     */
    private function detectBrowserLocale(Request $request): ?string
    {
        $languages = MultiLanguage::config('languages', []);

        foreach ($request->getLanguages() as $browserLocale) {
            $localeCode = substr($browserLocale, 0, 2);
            if (array_key_exists($localeCode, $languages)) {
                return $localeCode;
            }
        }

        return null;
    }

    /**
     * Сохранить локаль пользователя в БД
     */
    private function saveUserLocale($user, string $locale): void
    {
        if (!$user) {
            return;
        }

        try {
            $user->locale = $locale;
            $user->saveQuietly(['locale']);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to save user locale', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Загрузить все переводы для локали
     */
    private function loadTranslations(string $locale): array
    {
        $translations = [];

        // Путь к языковым файлам проекта
        $projectPath = base_path('resources/lang/' . $locale);

        // Загружаем из проекта
        if (is_dir($projectPath)) {
            foreach (glob($projectPath . "/*.php") as $file) {
                $group = basename($file, '.php');
                $translations[$group] = require $file;
            }
        }

        // Опционально: загружаем из пакета как fallback
        if (config('multi-language.translations.load_vendor', true)) {
            $packagePath = __DIR__ . '/../../../../resources/lang/' . $locale;

            if (is_dir($packagePath)) {
                foreach (glob($packagePath . "/*.php") as $file) {
                    $group = basename($file, '.php');

                    // Не перезаписываем существующие переводы из проекта
                    if (!isset($translations[$group])) {
                        $translations[$group] = require $file;
                    }
                }
            }
        }

        return $translations;
    }

    /**
     * Загрузить группу переводов
     */
    private function loadTranslationGroup(string $locale, string $group): array
    {
        $translations = [];

        // Сначала ищем в проекте
        $projectPath = base_path('resources/lang/' . $locale . '/' . $group . '.php');

        if (file_exists($projectPath)) {
            $translations = require $projectPath;
        }

        // Если не нашли и разрешено, ищем в пакете
        if (empty($translations) && config('multi-language.translations.load_vendor', true)) {
            $packagePath = __DIR__ . '/../../../../resources/lang/' . $locale . '/' . $group . '.php';

            if (file_exists($packagePath)) {
                $translations = require $packagePath;
            }
        }

        return $translations;
    }

    /**
     * Получить перевод с учетом fallback
     */
    private function translate(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('multi-language.translations.fallback', 'en');

        // Пытаемся получить перевод на текущем языке
        $translation = __("multi-language.{$key}", $replace, $locale);

        // Если перевода нет, пробуем на языке fallback
        if ($translation === "multi-language.{$key}" && $locale !== $fallback) {
            $translation = __("multi-language.{$key}", $replace, $fallback);
        }

        // Если всё еще нет, возвращаем ключ
        if ($translation === "multi-language.{$key}") {
            return $key;
        }

        return $translation;
    }
}
