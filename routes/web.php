<?php

use Illuminate\Support\Facades\Route;
use OpenAdminCore\Admin\MultiLanguage\Http\Controllers\MultiLanguageController;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;

// Маршрут для установки локали
Route::post('/locale', MultiLanguageController::class.'@locale');

// Условный маршрут для страницы входа
if(MultiLanguage::config("show-login-page", true)) {
    Route::get('auth/login', MultiLanguageController::class.'@getLogin');
}
