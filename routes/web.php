<?php

use Illuminate\Support\Facades\Route;
use OpenAdminCore\Admin\MultiLanguage\Http\Controllers\MultiLanguageController;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;

Route::post('/locale', MultiLanguageController::class.'@locale');
if(MultiLanguage::config("show-login-page", true)) {
    Route::get('auth/login', MultiLanguageController::class.'@getLogin');
}
