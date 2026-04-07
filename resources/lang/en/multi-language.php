<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Language Resources
    |--------------------------------------------------------------------------
    */

    // Success responses
    'success' => 'Success',
    'locale_changed' => 'Language changed successfully',
    'locale_updated' => 'User language updated successfully',
    'locale_saved' => 'Language saved successfully',

    // Error messages
    'unauthorized' => 'Unauthorized',
    'forbidden' => 'Forbidden',
    'not_found' => 'Resource not found',
    'server_error' => 'Internal server error',
    'too_many_requests' => 'Too many requests. Please try again in :seconds seconds',

    // Locale validation errors
    'locale_required' => 'Locale is required',
    'locale_invalid' => 'Invalid locale',
    'locale_not_supported' => 'Locale ":locale" is not supported',
    'locale_size' => 'Locale must be 2 characters',
    'locale_alpha' => 'Locale must contain only letters',

    // Informational messages
    'languages_list' => 'Available languages list',
    'current_locale' => 'Current locale',
    'user_locale' => 'User locale',
    'translations_list' => 'Translations list',
    'stats' => 'Language usage statistics',
    'validation_result' => 'Validation result',

    // HTTP statuses
    'http_200' => 'OK',
    'http_400' => 'Bad request',
    'http_401' => 'Unauthorized',
    'http_403' => 'Forbidden',
    'http_404' => 'Not found',
    'http_422' => 'Validation error',
    'http_429' => 'Too many requests',
    'http_500' => 'Internal server error',

    // Field attributes for validation
    'attributes' => [
        'locale' => 'locale',
        'username' => 'username',
        'password' => 'password',
        'remember' => 'remember me',
        'group' => 'group',
    ],
];
