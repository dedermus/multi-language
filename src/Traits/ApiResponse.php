<?php

namespace OpenAdminCore\Admin\MultiLanguage\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Успешный ответ
     */
    protected function successResponse(
        array $data = [],
        string $messageKey = 'success',
        int $statusCode = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $this->translate($messageKey),
            'data' => $data,
            'meta' => array_merge([
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String()
            ], $meta)
        ], $statusCode);
    }

    /**
     * Ответ с ошибкой
     */
    protected function errorResponse(
        string $errorKey,
        int $statusCode = Response::HTTP_BAD_REQUEST,
        array $data = [],
        array $replace = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $this->translate($errorKey, $replace),
                'http_message' => $this->translate("http_{$statusCode}")
            ],
            'data' => $data,
            'meta' => [
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String()
            ]
        ], $statusCode);
    }

    /**
     * Ответ с ошибкой валидации
     */
    protected function validationErrorResponse(
        $errors,
        string $errorKey = 'locale_invalid',
        int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $this->translate($errorKey),
                'http_message' => $this->translate("http_{$statusCode}"),
                'validation' => $errors
            ],
            'meta' => [
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String()
            ]
        ], $statusCode);
    }

    /**
     * Ответ со списком языков
     */
    protected function languagesResponse(array $languages, string $current, string $default): JsonResponse
    {
        return $this->successResponse(
            [
                'languages' => $languages,
                'current' => $current,
                'default' => $default,
                'total' => count($languages)
            ],
            'languages_list'
        );
    }

    /**
     * Ответ с текущей локалью
     */
    protected function currentLocaleResponse(string $locale, string $source, ?string $name = null): JsonResponse
    {
        return $this->successResponse(
            [
                'locale' => $locale,
                'source' => $source,
                'name' => $name ?? $locale
            ],
            'current_locale'
        );
    }

    /**
     * Ответ после установки локали
     */
    protected function localeChangedResponse(string $locale, string $name): JsonResponse
    {
        return $this->successResponse(
            [
                'locale' => $locale,
                'name' => $name,
                'changed_at' => now()->toIso8601String()
            ],
            'locale_changed'
        );
    }

    /**
     * Ответ с локалью пользователя
     */
    protected function userLocaleResponse($user, string $locale, string $name): JsonResponse
    {
        return $this->successResponse(
            [
                'user_id' => $user->id,
                'locale' => $locale,
                'name' => $name,
                'updated_at' => $user->updated_at?->toIso8601String()
            ],
            'user_locale'
        );
    }

    /**
     * Ответ после обновления локали пользователя
     */
    protected function userLocaleUpdatedResponse($user, string $locale, string $oldLocale, string $name): JsonResponse
    {
        return $this->successResponse(
            [
                'user_id' => $user->id,
                'locale' => $locale,
                'old_locale' => $oldLocale,
                'name' => $name,
                'updated_at' => now()->toIso8601String()
            ],
            'locale_updated'
        );
    }

    /**
     * Ответ с переводами
     */
    protected function translationsResponse(string $locale, array $translations): JsonResponse
    {
        return $this->successResponse(
            [
                'locale' => $locale,
                'translations' => $translations,
                'count' => count($translations)
            ],
            'translations_list'
        );
    }

    /**
     * Ответ с группой переводов
     */
    protected function translationGroupResponse(string $locale, string $group, array $translations): JsonResponse
    {
        return $this->successResponse(
            [
                'locale' => $locale,
                'group' => $group,
                'translations' => $translations,
                'count' => count($translations)
            ],
            'translations_list'
        );
    }

    /**
     * Ответ с результатом валидации
     */
    protected function validationResponse(string $locale, bool $isValid, array $availableLocales): JsonResponse
    {
        return $this->successResponse(
            [
                'valid' => $isValid,
                'locale' => $locale,
                'name' => $isValid ? $this->getLocaleName($locale) : null,
                'available_locales' => $availableLocales
            ],
            $isValid ? 'success' : 'locale_invalid'
        );
    }

    /**
     * Ответ со статистикой
     */
    protected function statsResponse(array $stats, array $languages, int $totalUsers): JsonResponse
    {
        return $this->successResponse(
            [
                'total_users' => $totalUsers,
                'distribution' => $stats,
                'languages' => $languages
            ],
            'stats'
        );
    }

    /**
     * Перевод сообщения
     */
    protected function translate(string $key, array $replace = []): string
    {
        $translation = __("multi-language.{$key}", $replace);

        // Если перевод не найден, возвращаем ключ
        if ($translation === "multi-language.{$key}") {
            return $key;
        }

        return $translation;
    }

    /**
     * Получить название локали
     */
    protected function getLocaleName(string $locale): string
    {
        $languages = config('multi-language.languages', []);
        return $languages[$locale] ?? $locale;
    }

    /**
     * Получить сообщение об ошибке для HTTP статуса
     */
    protected function getHttpStatusMessage(int $statusCode): string
    {
        return $this->translate("http_{$statusCode}");
    }
}
