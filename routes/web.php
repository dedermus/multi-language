<?php

use Illuminate\Support\Facades\Route;
use OpenAdminCore\Admin\MultiLanguage\Http\Controllers\MultiLanguageController;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;

if (MultiLanguage::config('enable', true)) {
    // Явно указываем middleware 'web'
    Route::post('/locale', [MultiLanguageController::class, 'locale'])
        ->middleware('web');

    if (MultiLanguage::config("show-login-page", true)) {
        Route::get('auth/login', [MultiLanguageController::class, 'getLogin']);
    }
}
