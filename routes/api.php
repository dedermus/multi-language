<?php

use Illuminate\Support\Facades\Route;
use OpenAdminCore\Admin\MultiLanguage\Http\Controllers\Api\MultiLanguageApiController;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;

// Префикс для API
$apiPrefix = 'admin/api';

if (MultiLanguage::config('enable', true)) {
    Route::prefix($apiPrefix)
        ->middleware(['web', 'api'])
        ->group(function () {

            // Публичные endpoints (не требуют авторизации)
            Route::get('/languages', [MultiLanguageApiController::class, 'getLanguages']);
            Route::get('/locale', [MultiLanguageApiController::class, 'getLocale']);
            Route::post('/locale', [MultiLanguageApiController::class, 'setLocale']);
            Route::post('/validate', [MultiLanguageApiController::class, 'validateLocale']);

            // Endpoints для переводов (публичные)
            Route::get('/translations/{locale}', [MultiLanguageApiController::class, 'getTranslations']);
            Route::get('/translations/{locale}/{group}', [MultiLanguageApiController::class, 'getTranslationGroup']);

            // Endpoints требующие авторизации
            Route::middleware(['admin.auth'])->group(function () {
                Route::get('/locale/user', [MultiLanguageApiController::class, 'getUserLocale']);
                Route::put('/locale/user', [MultiLanguageApiController::class, 'updateUserLocale']);

                // Админские endpoints
                Route::middleware(['admin.permission:administrator'])->group(function () {
                    Route::get('/stats', [MultiLanguageApiController::class, 'getStats']);
                });
            });
        });
}
